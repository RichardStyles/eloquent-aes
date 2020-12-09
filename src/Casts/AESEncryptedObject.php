<?php

namespace RichardStyles\EloquentAES\Casts;

use Illuminate\Database\Eloquent\JsonEncodingException;

class AESEncryptedObject extends AESEncrypted
{
    /**
     * Cast the given value and decrypt
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return object
     */
    public function get($model, $key, $value, $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        $decoded = parent::get($model, $key, $value, $attributes);

        // The encrypter should already json-decode this, but we’ll handle it too just in case.
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, false);
        }

        return $decoded;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        $value = json_encode($value);

        if ($value === false) {
            throw JsonEncodingException::forAttribute($this, $key, json_last_error_msg());
        }

        return parent::set($model, $key, $value, $attributes);
    }
}
