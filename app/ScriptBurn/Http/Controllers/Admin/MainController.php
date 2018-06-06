<?php

namespace App\ScriptBurn\Http\Controllers\Admin;

use Backpack\Base\app\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MainController extends Controller
{
	private $speechAapi;
	public function __construct()
	{
		$this->speechAapi = resolve('speech');
	}
	public function download(Request $request, $file)
	{
		$file = str_replace(["/", "\\"], "", $file);
		$file_path = storage_path('output/audio/'.$file.".mp3");
		if (!file_exists($file_path))
		{
			return abort(404);
		}

		return \Response::download($file_path, $file.".mp3", [
			'Content-Length'=>@filesize($file_path),
			'Content-Type'=> 'audio/mpeg',
		]);
	}

	public function speech(Request $request)
	{
		try
		{
			$ret = $this->speechAapi->textToSpeech(
				$request->input('speech_text'),
				$request->input('speech_voice_name'),
				"",
				$request->input('speech_voice_speed'),
				$request->input('speech_voice_pitch'),
				$request->input('speech_ssml')

			);

			if (!$ret['status'])
			{
				throw new \Exception($ret['data']);
			}
			unset($ret['data']['text']);
			unset($ret['data']['file']);
			$response = $ret;
		}
		catch (\Exception $e)
		{
			$response = ['status' => 0, 'data' => $e->getMessage()];
		}
		finally
		{
			sleep(1);

			return response()->json($response);
		}
	}

	public function home(Request $request)
	{
		try
		{
			$langs = $this->speechAapi->getLanguages();
			//p_d($langs);
			$rows = [];
			foreach ($langs as $key => $lang)
			{
				do
				{
					$lang_key = array_search($lang['Language'], array_column($langs, 'Language'));
					if ($lang_key === false)
					{
						break;
					}
					$item = $langs[$lang_key];
					$rows[$lang['Language']]['voice_type'][$item['Voice type']][$item['Voice name']] = $item['Voice name'];

					unset($langs[$lang_key]);
					$langs = array_values($langs);
				} while (true);
			}

			$data['voices'] = $rows;

			$data['cur_lang'] = $request->speech_lang ?: 'English (US)';

			return view('speech/home', $data);
		}
		catch (\Exception $e)
		{
			\Alert::error($e->getMessage())->flash();

			return redirect(route('home'));
		}
	}
}
