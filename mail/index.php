<?php
include '../kernel/config.php';

$message ='
	<div style="background:#333">
	<div style="background:#FFF;max-width:800px;margin:0 auto;color:#353535;font:\'Arial\';font-size:15px;line-height:20px">
		<div style="width: 100%;background: url(http://teen-quotes.com/mail/bg_header.jpg) repeat-x;height: 42px;padding-top: 18px;display: block;">
			<a href="http://teen-quotes.com" title="Teen Quotes Website" style="width: 196px;height: 30px;margin-left: 20px;background: url(http://teen-quotes.com/mail/logo_teenquotes.png) no-repeat;display: block;"></a>
		</div>
		<div style="background:#333">
			<a href="http://teen-quotes.com/apps" title="Download the iOS application" style="width: 300px;height:300px;background: url(http://teen-quotes.com/mail/promo_300_300.png) no-repeat;margin: 0 auto;display: block;"></a>
		</div>
		<div style="padding:10px;background: #EFEFEF">
			Hi <font color="#394DAC"><b>\'.$username.\'</b></font>!<br/>
			<br/>
			Today we’ve got a big annoucement for you! <b>Teen Quotes is now available right from your iPhone or your iTouch</b> thanks to our brand new application.<br/>
			<br/>
			Do not ever leave Teen Quotes. Free, easy to use and fast, this application offers the website\'s best functionalities.<br/>
			<br/>
			<ul style="list-style:square">
				<li>Browse quotes, <font color="#394DAC">even if you\'re offline</font>.</li>
				<li>Create your account, or sign in if you have already one.</li>
				<li>Submit quotes and add comments.</li>
				<li>Share on Facebook, Twitter and via email.</li>
				<li>Add quotes to your favorites.</li>
			</ul>
			You can download the application right now : visit <a href="http://teen-quotes.com/apps" title="Teen Quotes application">teen-quotes.com/apps</a> from your iPhone / iTouch.<br/>
			<br/>
			By the way, we have also release a new version of the desktop version and the mobile version in order to suit the application’s design. We’re sure you’ll enjoy it!<br/>
			<br/>
			See you soon on Teen Quotes.<br/>
			<br/>
			Best regards,<br/>
			<b>The Teen Quotes Team</b>
			<br/><br/><br/>
			<div style="border-top:1px solid #CCCCCC">
				<br/>
				<img src="http://teen-quotes.com/images/icones/about.png" style="margin:0 10px 5px 15px;vertical-align:middle;height:20px;width:20px;"/>Website: <a href="http://teen-quotes.com" title="Website">teen-quotes.com</a><br/>
				<img src="http://teen-quotes.com/images/icones/french_big.png" style="margin:0 10px 5px 15px;vertical-align:middle;height:20px;width:20px;"/>Kotado: <a href="http://kotado.fr" title="Kotado">kotado.fr</a><br/>
				<img src="http://teen-quotes.com/images/icones/mobile.png" style="margin:0 10px 5px 15px;vertical-align:middle;height:20px;width:20px;"/>Mobile website: <a href="http://m.teen-quotes.com" title="Mobile website">m.teen-quotes.com</a><br/>
				<img src="http://teen-quotes.com/images/icones/facebook.png" style="margin:0 10px 5px 15px;vertical-align:middle;height:20px;width:20px;"/>Facebook: <a href="http://www.facebook.com/ohteenquotes" title="Facebook">www.facebook.com/ohteenquotes</a><br/>
				<img src="http://teen-quotes.com/images/icones/twitter.png" style="margin:0 10px 5px 15px;vertical-align:middle;height:20px;width:20px;"/>Twitter: <a href="http://twitter.com/ohteenquotes" title="Twitter">@ohteenquotes</a><br/>
			</div>
		</div>
	</div>
</div>';
mail('valerie.augusti@hotmail.fr', 'Test', $message, $headers);