<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUlids;

    /**
     * Nama kolom primary key.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Tipe data primary key adalah string (ULID).
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Nonaktifkan auto-increment karena menggunakan ULID.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id', // optional jika pakai HasUlids, bisa dihilangkan
        'name',
        'username',
        'email',
        'password',
        'email_verified_at',
    ];

    /**
     * Atribut yang harus disembunyikan saat serialisasi.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atribut yang harus di-cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Mutator untuk memastikan username selalu huruf kecil.
     *
     * @param string $value
     * @return void
     */
    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = strtolower($value);
    }
}
