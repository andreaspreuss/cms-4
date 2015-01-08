default:
	rm -rf tmp/*
	rm -rf log/*
	rm -rf public/Vestibulum.zip && zip -r public/Vestibulum.zip public/* src/* tmp vendor/latte vendor/erusev .htaccess readme.md LICENSE