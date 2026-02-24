<?php

namespace RichardStyles\EloquentAES\Casts;

use Illuminate\Database\Eloquent\JsonEncodingException;
use Illuminate\Support\Collection;

class AESEncryptedCollection extends AESEncrypted
{
    /**
     * Cast the given value and decrypt
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     * @return \Illuminate\Support\Collection<int, mixed>|null
     */
    public function get($model, $key, $value, $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        return new Collection(json_decode(parent::get($model, $key, $value, $attributes)));
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     * @return string|null
     */
    public function set($model, $key, $value, $attributes)
    {
        if (is_null($value)) {
            return null;
        }

        if ($value instanceof Collection) {
            $value = $value->toJson();
        } else {
            $value = json_encode($value);

            if ($value === false) {
                throw JsonEncodingException::forAttribute($this, $key, json_last_error_msg());
            }
        }

        return parent::set($model, $key, $value, $attributes);
    }
}
