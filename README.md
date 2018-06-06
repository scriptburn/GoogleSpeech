![alt text](https://raw.githubusercontent.com/scriptburn/googlespeech/master/screenshot.png)


### Installation


```sh
$ composer  create-project scriptburn/googlespeech 
```

### Make sure to update Db connection string in .env file 
### and update GOOGLE_APP_CRED inside .env with the name of your google credential json file which must must be copied on root of application (not in public folder)

### make sure to give storage folder write permission
### create folder storage/framework , storage/framework/cache ,storage/framework/sessions ,storage/framework/views
## publish resources with "php artisan vendor:publish " and choose adminlte