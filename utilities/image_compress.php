<?php
class ImageCompressor {

    private $source_path;   // Define source path as a private class variable
    private $destination_path; // Define destination path as a private class variable
    private $name;
    private $originalWidth = null;
    private $sizes = [
        'full' => 1920,
        'large' => 1280,      // Width = 1280px, height auto
        'medium_large' => 720,// Width = 720px, height auto
        'medium' => 360,      // Width = 360px, height auto
        'thumbnail' => 150    // Width = 150px, height = 150px (square)
    ];
    // Constructor to initialize the source path and destination path
    public function __construct($source_path, $destination_path = '') {
        $this->source_path = $source_path;

        // If destination_path is not provided, set it to the same as source_path
        $this->destination_path = !empty($destination_path) ? $destination_path : $this->source_path;
        $this->name = preg_replace("/\.\w+$/","",basename($this->destination_path));
    }

    // Compress image method (based on mime type) and create scaled versions if first time
    public function compress_image($quality = 85, $create_scaled_versions = true) {
        // Ensure the source path is set
        if (!$this->source_path || !$this->destination_path) {
            return false; // No source or destination path defined
        }

        // Create destination folder if it doesn't exist
        $this->create_destination_folder();

        // Get the image info (type, etc.)
        $image_info = getimagesize($this->source_path);

        // If the image info is invalid, return false
        if (!$image_info) {
            return false;
        }
        $this->originalWidth = $image_info[0];

        // if ($this->originalWidth > $this->sizes['full']) {
        //   // code...
        // }
        // // Get the mime type
        // $mime_type = $image_info['mime'];
        //
        // // Compress the image based on mime type
        // switch ($mime_type) {
        //     case 'image/jpeg':
        //         $compression_result = $this->compress_jpeg($this->source_path, $this->destination_path, $quality);
        //         break;
        //
        //     case 'image/png':
        //         $compression_result = $this->compress_png($this->source_path, $this->destination_path, $quality);
        //         break;
        //
        //     case 'image/webp':
        //         $compression_result = $this->compress_webp($this->source_path, $this->destination_path, $quality);
        //         break;
        //
        //     default:
        //         return false; // Unsupported file type
        // }
        //
        // // If compression is successful and scaled versions are required
        // if ($compression_result && $create_scaled_versions) {
            // Create scaled versions of the image in the same directory as the destination
            $output_dir = dirname($this->destination_path); // Use the same directory for scaled versions
            $this->create_scaled_versions($output_dir);
        // }

        return true;
    }

    // Compress JPEG images
    private function compress_jpeg($source_path, $destination_path, $quality) {
        // Load JPEG image
        $image = imagecreatefromjpeg($source_path);
        if (!$image) {
            return false; // Image loading failed
        }

        // Compress and save JPEG image
        $result = imagejpeg($image, $destination_path, $quality); // 0 (worst quality) to 100 (best quality)

        // Free memory
        imagedestroy($image);

        return $result ? $destination_path : false;
    }

    // Compress PNG images
    private function compress_png($source_path, $destination_path, $quality) {
        // Load PNG image
        $image = imagecreatefrompng($source_path);
        if (!$image) {
            return false; // Image loading failed
        }

        // Set PNG compression level (0-9), quality directly affects compression
        $compression_level = 9 - ($quality / 10); // Higher compression with lower quality
        // Compress and save PNG image
        $result = imagepng($image, $destination_path, $compression_level);

        // Free memory
        imagedestroy($image);

        return $result ? $destination_path : false;
    }

    // Compress WebP images
    private function compress_webp($source_path, $destination_path, $quality) {
        // Load WebP image
        $image = imagecreatefromwebp($source_path);
        if (!$image) {
            return false; // Image loading failed
        }

        // Compress and save WebP image
        $result = imagewebp($image, $destination_path, $quality); // 0 (worst quality) to 100 (best quality)

        // Free memory
        imagedestroy($image);

        return $result ? $destination_path : false;
    }

    // Function to create scaled versions
    public function create_scaled_versions($output_dir) {
        $sizes = $this->sizes;

        // Loop through each size and generate the scaled images
        foreach ($sizes as $size_name => $size_value) {
            $this->create_scaled_image($size_name, $size_value, $output_dir);
        }
    }

    // Function to create a single scaled image based on width (or square for thumbnail)
    private function create_scaled_image($size_name, $size_value, $output_dir) {
        // Get image dimensions and mime type
        $image_info = getimagesize($this->source_path);
        $original_width = $image_info[0];
        $original_height = $image_info[1];
        $crop = null;

        $new_width = $size_value;
        if ($this->originalWidth < $new_width) {
          $new_width = $this->originalWidth;
        }
        $new_height = ($original_height / $original_width) * $new_width;

        if ($size_name === 'thumbnail'){
          $crop = true;
          $scale = ($new_width > $new_height) ? ($new_width / $new_height) : ($new_height / $new_width);
          $new_width = $new_width * $scale;
          $new_height = $new_height * $scale;
        }

        // Load the original image
        $mime_type = $image_info['mime'];
        switch ($mime_type) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($this->source_path);
                break;
            case 'image/png':
                $image = imagecreatefrompng($this->source_path);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($this->source_path);
                break;
            default:
                return false;
        }

        // Create a new empty image with the new dimensions
        $cords = [0,0,0,0];
        if ($size_name === 'thumbnail') {
          $scaled_image = imagecreatetruecolor($this->sizes['thumbnail'], $this->sizes['thumbnail']);
          if ($original_width > $original_height) {
            $cords[0] = -(($new_width - $this->sizes['thumbnail'] ) / 2);
          }else{
            $cords[1] = -(($new_height - $this->sizes['thumbnail'] ) / 2);
          }
        }else {
          $scaled_image = imagecreatetruecolor($new_width, $new_height);
        }

        // Preserve transparency for PNG and WebP
        if ($mime_type == 'image/png' || $mime_type == 'image/webp') {
            imagealphablending($scaled_image, false);
            imagesavealpha($scaled_image, true);
        }
        // Resize the original image into the scaled image
        imagecopyresampled($scaled_image, $image, $cords[0], $cords[1], $cords[2], $cords[3], $new_width, $new_height, $original_width, $original_height);

        // Define the destination file path for the scaled image
        // $destination_path = $output_dir . DIRECTORY_SEPARATOR . "$size_name.jpg";

        $size_name = $size_name == "full" ? "" : "_$size_name";
        $destination_path = dirname($this->destination_path) . "/{$this->name}{$size_name}.jpg";

        // Save the scaled image
        switch ($mime_type) {
            case 'image/jpeg':
                imagejpeg($scaled_image, $destination_path, 85); // Default quality
                break;
            case 'image/png':
                imagepng($scaled_image, $destination_path);
                break;
            case 'image/webp':
                imagewebp($scaled_image, $destination_path, 85); // Default quality
                break;
        }

        // Free memory
        imagedestroy($image);
        imagedestroy($scaled_image);

        return $destination_path;
    }

    // Function to create the destination folder if it doesn't exist
    private function create_destination_folder() {
        // Get the directory part of the destination path
        $destination_dir = dirname($this->destination_path);

        // If the destination folder doesn't exist, create it recursively
        if (!file_exists($destination_dir)) {
            mkdir($destination_dir, 0777, true); // Create the folder with full permissions
        }
    }
}

/*
Example Usage:
// Include the class (if it's in a separate file)
include 'path/to/ImageCompressor.php';

// Define the source path (image to compress)
$source_path = 'path/to/your/image.jpg';

// Define the destination path (optional)
$destination_path = 'path/to/destination/folder/compressed_image.jpg';

// Create an instance of the ImageCompressor class
$image_compressor = new ImageCompressor($source_path, $destination_path);

// Compress the image and create scaled versions (optional, can set to false if not needed)
$compressed_image = $image_compressor->compress_image(85, true);  // 85% quality for JPEG

if ($compressed_image) {
    echo 'Image compressed and scaled versions created successfully. Saved at: ' . $compressed_image;
} else {
    echo 'There was an error compressing the image.';
}

*/
