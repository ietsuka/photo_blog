<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Photo extends Model
{
    use HasFactory;

    /** JSONに含める属性 */
    protected $appends = [
      'url', 'likes_count', 'liked_by_user',
    ];
    /** JSONに含めない属性 */
    protected $hidden = [
      'user_id', 'filename',
      self::CREATED_AT, self::UPDATED_AT,
    ];
    /** JSONに含める属性 */
    protected $visible = [
      'id', 'owner', 'url', 'comments',
      'likes_count', 'liked_by_user',
    ];

    protected $perPage = 15;
    /** プライマリキーの型 */
    // protected $keyType = 'string';
    /** IDの桁数 */
    const FILENAME_LENGTH = 12;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (! Arr::get($this->attributes, 'filename')) {
            $this->setFilename();
        }
    }

    /**
     * ランダムなsetFilename値をsetFilename属性に代入する
     */
    private function setFilename()
    {
        $this->attributes['filename'] = $this->getRandomFilename();
    }

    /**
     * ランダムなsetFilename値を生成する
     * @return string
     */
    private function getRandomFilename()
    {
        $characters = array_merge(
            range(0, 9), range('a', 'z'),
            range('A', 'Z'), ['-', '_']
        );

        $length = count($characters);

        $filename = "";

        for ($i = 0; $i < self::FILENAME_LENGTH; $i++) {
            $filename .= $characters[random_int(0, $length - 1)];
        }

        return $filename;
    }

    /**
     * リレーションシップ - usersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id', 'users');
    }

    /**
     * アクセサ - url
     * @return string
     */
    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->attributes['filename']);
    }

    /**
     * リレーションシップ - commentsテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('id', 'desc');
    }

    /**
     * リレーションシップ - usersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    /**
     * アクセサ - likes_count
     * @return int
     */
    public function getLikesCountAttribute()
    {
        return $this->likes->count();
    }

    /**
     * アクセサ - liked_by_user
     * @return boolean
     */
    public function getLikedByUserAttribute()
    {
        if (Auth::guest()) {
            return false;
        }

        return $this->likes->contains(function ($user) {
            return $user->id === Auth::user()->id;
        });
    }

}
