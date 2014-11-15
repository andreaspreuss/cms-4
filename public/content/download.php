<?php
namespace vestibulum {

	if (file_exists('Vestibulum.zip')) redirect(url('/Vestibulum.zip')); // download latest version

	redirect('https://github.com/OzzyCzech/vestibulum/releases'); // redirect to releases

}