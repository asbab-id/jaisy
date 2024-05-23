<?php
  $static_folder = __DIR__ .'/../' . '/static/';
  
  // Whitelist ekstensi file yang diizinkan
  $allowed_extensions = array('css', 'jpg', 'jpeg', 'png', 'gif', 'mp4', 'html', 'js', 'svg');
  
  // Mendapatkan ekstensi file yang diminta
  $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
  
  // Memeriksa apakah ekstensi file diizinkan
  if (!in_array($file_extension, $allowed_extensions)) {
      http_response_code(403);
      echo "Forbidden";
      exit();
  }
  
  // Membentuk path lengkap file
  $full_path = $static_folder . $filename;
  
  // Include file jika ada
  if (file_exists($full_path)) {
      // Mendapatkan tipe konten berdasarkan ekstensi file
      if($file_extension == 'css'){
        $content_type = 'text/css';
      }elseif($file_extension == 'js'){
        $content_type = 'text/javascript';
      }else{
        $content_type = mime_content_type($full_path);
      }
      
      // Set header Content-Type
      header("Content-Type: $content_type");
      
      // Tampilkan file
      readfile($full_path);
  } else {
      http_response_code(404);
      echo "Not Found";
  }