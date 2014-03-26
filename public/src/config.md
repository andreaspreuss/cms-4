
# Config

Best way how to

    server {
	    listen                *:80;
	    server_name           vestibulum.dev;

	    root   /Users/roman/Work/vestibulum/public;

	    location / {
		    try_files  $uri  $uri/  /index.php?$args;
		    index index.php;
	    }

        location ~ \.php$ {
        try_files  $uri  $uri/  /index.php?$args;
        index  index.html index.htm index.php;

        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;


        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_intercept_errors on;
        include fastcgi_params;
      }
    }