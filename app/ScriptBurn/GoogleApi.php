<?php
namespace App\Scriptburn;
class GoogleApi
{
	private $credentialsFile, $project, $youTubeService, $speechService, $wordList, $db, $cache, $langList, $outputFolder, $seperateFolder, $speechObject;
	private $ffprobe, $nlFormat, $nl, $defaultSepNum, $youTubeAPi;
	private $defaultVideoTitle, $defaultVideoDescription, $defaultVideoCat, $defaultVideoTags;
	public function __construct($outputFolder, $seperateFolder, $credentialsFile)
	{
		global $argv;
		$this->outputFolder = $outputFolder;
		$this->seperateFolder = $seperateFolder;
		$this->credentialsFile = $credentialsFile;
		$this->project = $this->fetchProjectId();
		$this->client = new \Google_Client();
		$this->client->useApplicationDefaultCredentials();
		$this->client->addScope(\Google_Service_Texttospeech::CLOUD_PLATFORM);
		$this->speechService = new \Google_Service_Texttospeech($this->client);
		$this->checkFolders();
		if (isset($argv) && count($argv))
		{
			$this->nlFormat = '%1$s';
			$this->nl = "\n";
			$this->defaultSepNum = 1;
		}
		else
		{
			$this->nlFormat = '<pre>%1$s</pre>';
			$this->nl = "</br>";
			$this->defaultSepNum = 0;
		}
	}
	public function printMsg($msg, $sepNum = 1)
	{
		echo (sprintf($this->nlFormat, $msg).str_repeat($this->nl, is_null($sepNum) ? $this->defaultSepNum : $sepNum));
	}
	private function checkFolders()
	{
		if ($this->seperateFolder)
		{
			if (!is_writable($this->getAudioFolder()))
			{
				if (!\File::makeDirectory($this->getAudioFolder(), 0775, true))
				{
					throw new Exception("Unable to create folder: ".$this->getAudioFolder());
				}
			}
		}
	}
	public function getAudioFolder()
	{
		return $this->seperateFolder ? $this->outputFolder."/audio" : $this->outputFolder;
	}
	private function getClient()
	{
		return $this->client;
	}
	private function speechClient()
	{
		putenv('GOOGLE_APPLICATION_CREDENTIALS='.$this->credentialsFile);

		return $this->speechService;
	}
	private function validateLangCode($code, $index = "Language code")
	{
		$langs = $this->getLanguages();
		$index = array_search($code, array_column($langs, $index));
		if ($index === false)
		{
			return $index;
		}
		else
		{
			return $langs[$index];
		}
	}
	public function getLanguages()
	{
		if ($this->langList)
		{
			return $this->langList;
		}
		$this->langList = [];
		$header = [];
		$row = 1;
		if (($handle = fopen(realpath(__DIR__."/../../")."/lang.csv", "r")) !== FALSE)
		{
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
			{
				if ($row == 1)
				{
					$header = $data;
					$row++;
					continue;
				}
				$data = array_combine($header, $data);
				$this->langList[$data['Voice name']] = $data;
				$row++;
			}
			fclose($handle);
			$this->langList = array_values($this->langList);
		}

		return $this->langList;
	}
	private function validateSpeechInput($langCode, $gender = "")
	{
		$allowed_gender = ['MALE', 'FEMALE', 'NEUTRAL'];
		$langs = $this->getLanguages();
		$byVoice = false;
		if ($lang = $this->validateLangCode($langCode, "Voice name"))
		{
			$gender = $lang['SSML Gender'];
			$byVoice = true;
		}
		elseif (!($lang = $this->validateLangCode($langCode, "Language code")))
		{
			$langs = array_keys(array_flip(array_column($langs, 'Language code')));
			throw new \Exception("Invalid Language code ($langCode), Choose from: ".implode(", ", $langs));
		}
		elseif (!in_array($gender, $allowed_gender))
		{
			throw new \Exception("Please specify a speech gender $gender, Choose from:".implode(", ", $allowed_gender));
		}

		return [
			'lang_code' => $lang['Language code'],
			'lang_desc' => $lang['Language'],
			'lang_name' => $lang['Voice name'],
			'lang_gender' => $gender,
			'by_voice' => (int) $byVoice];
	}
	public function textToSpeech($text, $langCode, $gender = "", $speed = "1", $pitch = "0", $isssml = false, $callback = null)
	{
		try
		{
			$speed = (float) $speed;
			$pitch = (float) $pitch;
			$startTime = microtime(true);
			$lang = $this->validateSpeechInput($langCode, $gender);
			$name_postfix = [];
			if (!$text)
			{
				throw new \Exception("You must provide some text");
			}
			if ($lang['by_voice'])
			{
				$speechObject = $this->getSpeechObject($lang['lang_name'], "", "", $speed, $pitch);
				$name_postfix[] = $lang['lang_name'];
				$name_postfix[] = $lang['lang_gender'];
			}
			else
			{
				$speechObject = $this->getSpeechObject($lang['lang_name'], $lang['lang_code'], $lang['lang_gender'], $speed, $pitch);
				$name_postfix[] = $lang['lang_code'];
				$name_postfix[] = $lang['lang_gender'];
			}
			$name_postfix[] = $speed;
			$name_postfix[] = $pitch;
			$name_postfix[] = (int)$isssml;
			 
			if ($speed < .25 || $speed > 4)
			{
				throw new \Exception("Speed should be in range of 0.25 to 4");
			}
			elseif ($pitch < -20 || $pitch > 20)
			{
				throw new \Exception("Pitch should be in range of 20 to 20");
			}
			$text = str_replace([chr(13)], "", $text);
			if (strlen($text) > 20)
			{
				$name = substr($text, 0, 20)." ".md5($text);
			}
			else
			{
				$name = $text;
			}
			$fileName = strtolower($this->createSlug($name)."_".$this->createSlug(implode("-", $name_postfix)));
			$filepath = $this->getAudioFolder()."/".$fileName.".mp3";
			if (!@filesize($filepath))
			{
				$input = new \Google_Service_Texttospeech_SynthesisInput();
				if ($isssml)
				{
					$input->setSsml($text);
				}
				else
				{
					$input->setText($text);
				}
				$speechObject->setInput($input);
				$result = $this->speechClient()->text->synthesize($speechObject);
				if (get_class($result) != 'Google_Service_Texttospeech_SynthesizeSpeechResponse')
				{
					throw new \Exception("Invalid Texttospeech response");
				}
				$fp = fopen($filepath, "w");
				fwrite($fp, base64_decode($result->getAudioContent()));
				fclose($fp);
			}
			$response = [
				'status' => 1,
				'data' => array_merge($lang, [
					'text' => $text,
					'file' => $filepath,
					'file_name' => $fileName,
				]),
			];
		}
		catch (\Exception $e)
		{
			$response = ['status' => 0, 'data' => $this->decodeResponseError($e)];
		}
		finally
		{
			$response['time'] = number_format(microtime(true) - $startTime, 2);
		}

		return $response;
	}
	private function getSpeechObject($langName, $langCode = "", $gender = "", $speed = "1", $pitch = "0")
	{
		if (!$langCode && !$gender)
		{
			$index = $langName;
		}
		else
		{
			$index = $langCode."-".$gender;
		}
		if (!isset($this->speechObject[$index]))
		{
			$audioConfig = new \Google_Service_Texttospeech_AudioConfig();
			$audioConfig->setAudioEncoding('MP3');
			$audioConfig->setPitch($pitch);
			$audioConfig->setSpeakingRate($speed);
			$voice = new \Google_Service_Texttospeech_VoiceSelectionParams();
			if (!$langCode && !$gender)
			{
				$lang = $this->validateLangCode($langName, "Voice name");
				//p_D($lang);
				$voice->setName($lang['Voice name']);
				$voice->setLanguageCode($lang['Language code']);
				$voice->setSsmlGender($lang['SSML Gender']);
			}
			else
			{
				$voice->setLanguageCode($langCode);
				$voice->setSsmlGender($gender);
			}
			//p_d("$langName, $langCode , $gender");
			$this->speechObject[$index] = new \Google_Service_Texttospeech_SynthesizeSpeechRequest();
			$this->speechObject[$index]->setAudioConfig($audioConfig);
			$this->speechObject[$index]->setVoice($voice);
		}

		return $this->speechObject[$index];
	}
	private function fetchProjectId()
	{
		if (@filesize($this->credentialsFile))
		{
			$json = @json_decode(file_get_contents($this->credentialsFile), JSON_OBJECT_AS_ARRAY);
			if (@$json['project_id'])
			{
				return trim($json['project_id']);
			}
		}
	}
	public function decodeResponseError($e, $searchReplace = [])
	{
		$msg = $e->getMessage();
		$errObj = json_decode($e->getMessage(), JSON_OBJECT_AS_ARRAY);
		$fnMatchthis = function ($rule, $err, $searchReplace = [])
		{
			$matches = [];
			$match = preg_match_all($rule, $err, $matches, PREG_SET_ORDER, 0);
			//p_d($matches[0][1]);
			if (!empty($matches[0][1]))
			{
				$str = explode("/", $matches[0][1]);
				$err = str_replace($matches[0][1], $str[count($str) - 1], $err);
				foreach ($searchReplace as $key => $value)
				{
					$err = str_replace($key, $value, $err);
				}
			}

			return $err;
		};
		if (is_array($errObj))
		{
			$err = "";
			if (!empty($errObj['error']['errors']))
			{
				$err = $errObj['error']['errors'][0]['message'];
			}
			elseif (!empty($errObj['error_description']))
			{
				$err = $errObj['error_description'];
			}
			if ($err)
			{
				if (stripos($err, 'Invalid JWT Signature.') !== false)
				{
					$err = "You do not have permission on this project"; //"Your access to the project: '{$this->project}' seems to be revoked";
				}
				else
				{
					$err = $fnMatchthis("/The resource ['](.*)['] already exists/", $err, $searchReplace);
					$err = $fnMatchthis("/The resource ['](.*)['] was not found/", $err, $searchReplace);
				}

				return $err;
			}
		}

		return $msg;
	}
	function curl_get_responce_contents($url)
	{
		$client = new \GuzzleHttp\Client();
		$response = $client->request('GET', $url);

		return (string) $response->getBody();
	}
	public function createSlug($str, $delimiter = '-')
	{
		$slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));

		return $slug;
	}
}