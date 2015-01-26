default:
	rm -rf tmp/*
	rm -rf log/*
	rm -rf public/Vestibulum.zip && zip -r public/Vestibulum.zip public/* src/* tmp vendor/latte vendor/erusev .htaccess readme.md LICENSE

update:
	wget -q https://raw.githubusercontent.com/sphido/events/master/src/events.php -O ./src/events.php
	wget -q https://raw.githubusercontent.com/sphido/routing/master/src/routing.php -O ./src/routing.php
	wget -q https://raw.githubusercontent.com/sphido/config/master/src/config.php -O ./src/config.php