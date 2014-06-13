#!/bin/bash
compass compile;
cd public/assets/css;
cat cosmo.css animate.css font-awesome.css ie.css print.css screen.css > styles.min.css 
cd ../js;
cat jquery-2.1.0.min.js bootstrap.min.js detect.js ads.js mailgun-validator.js app.js > scripts.min.js