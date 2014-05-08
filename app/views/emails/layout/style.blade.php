<style>
	#background {
		background-color: #FFFFFF;
		margin-bottom: 40px;
	}

	#container {
		background: #F2F2F2;
		max-width: 800px;
		margin: 0 auto;
		color: #353535;
		font-family: 'Arial';
		font-size: 14px;
		line-height: 20px
	}

	#container #logo-container {
		width: 100%;
		background-color: #22313f;
		height: 42px;
		padding-top: 18px;
		display: block;
	}

	#container #logo-container a {
		margin-left:10px;
		width:196px;
		height:30px;
		background: url({{ Lang::get('email.URLLogo') }}) no-repeat;
		display: block;
	}

	#container #body-content {
		padding: 10px;
	}

	#container #footer-container {
		padding-bottom:10px;
		border-top:1px solid #CCCCCC
	}

	#container #footer-container img.icon {
		margin: 0 10px 5px 15px;
		vertical-align: middle;
		height: 20px;
		width: 20px;
	}

	#footer-disclaimer {
		font-size: 90%;
	}
</style>