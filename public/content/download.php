<?php
namespace cms {

	if (file_exists('Sphido.zip')) redirect(url('/Sphido.zip')); // download latest version

	redirect('https://github.com/sphido/cms/releases'); // redirect to releases

}