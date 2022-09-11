<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>
<a href="https://www.sendinblue.com/" class="logo">
<img class="logo-color skip-lazy" src="https://www.sendinblue.com/wp-content/themes/sendinblue2019/assets/images/common/logo-color.svg" alt="">
</a>
<p align="center">
</p>

## composer install
## register https://slack.com/get-started#/createnew  and give token
## file .env set SLACK_TOKEN=your token
## Step 1: Add Configuration
First you need to create account on sendinblue if you don't have. So click bellow link to create account:

Sendinblue Site: https://www.sendinblue.com
After creating account and domain active you have to go bellow url as like bellow screen shot:

Setup Page: https://account.sendinblue.com/advanced/api

you have to pickup, Login and Secret with other configuration.

add details from there bellow:

.env

- MAIL_DRIVER=smtp
- MAIL_HOST=smtp-relay.sendinblue.com
- MAIL_PORT=587
- MAIL_USERNAME=your_email@proton.me
- MAIL_PASSWORD=k3R4zwKhECNfjjPD
- MAIL_ENCRYPTION=tls
- MAIL_FROM_ADDRESS=your_email_sender@proton.me
- MAIL_FROM_NAME="${app-name}"
- SENDINBLUE_SENDER=your_email_sender@proton.me
- SENDINBLUE_API_KEY=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

##API methods — This API allows you to build your own customized Email clients.
##api/notification/send/email/attachment POST
request:

{
- file:mimes:pdf, jpg, jpeg, png, bmp, gif, svg, or webp
- title:Mail from sendinBlue api--title
- body:Mail from sendinBlue api--body
- email:hanie1@gmail.com,hanie2@gmail.com,hanie3@gmail.com

  }

response:
{
ایمیل ارسال شد.
}
