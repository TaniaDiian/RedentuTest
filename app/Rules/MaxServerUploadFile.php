<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MaxServerUploadFile implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $file_size;
    private $max_size;

    public function __construct()
    {
        //
        $max_upload = (int)(ini_get('upload_max_filesize'));
        $max_post = (int)(ini_get('post_max_size'));
        $memory_limit = (int)(ini_get('memory_limit'));
  
        $upload_mb = min($max_upload, $max_post, $memory_limit);
  
        $max_size =  $upload_mb < 0 ? 2000 :  $upload_mb ;
        $this->max_size = $max_size;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $this->file_size = round($value->getSize() / 1024, 2);
        return  $this->max_size > $this->file_size;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "Max upload size $this->max_size kb; file size $this->file_size kb;";
    }
}
