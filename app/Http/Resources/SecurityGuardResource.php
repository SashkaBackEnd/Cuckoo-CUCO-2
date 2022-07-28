<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SecurityGuardResource extends JsonResource
{
    /**
     * @var bool
     */
    private static $addLastCheck = false;

    /**
     * @param bool $add
     */
    public static function addLastCheck(bool $add)
    {
        self::$addLastCheck = $add;
    }

    /**
     * @var bool
     */
    private static $addCurrentShift = false;

    /**
     * @param bool $add
     */
    public static function addCurrentShift(bool $add)
    {
        self::$addCurrentShift = $add;
    }

    /**
     * @var bool
     */
    private static $addLog = false;

    /**
     * @param bool $add
     */
    public static function addLog(bool $add)
    {
        self::$addLog = $add;
    }
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'gun' => $this->gun,
            'car' => $this->car,
            'pin' => $this->pin,
            'name' => $this->name,
            'debts' => $this->debts,
            'phone' => $this->phone == null ? null : transformPhoneByMask($this->phone),
            'status' => $this->status,
            'surname' => $this->surname,
            'license' => $this->license,
            'comment' => $this->comment,
            'workType' => $this->work_type,
            'patronymic' => $this->patronymic,
            'leftThings' => $this->left_things,
            'licenseRank' => $this->license_rank,
            'medicalBook' => $this->medical_book,
            'knewAboutUs' => $this->knew_about_us,
            'drivingLicense' => $this->driving_license,
            'birthDate' => strtotime($this->birth_date),
            'licenseToDate' => strtotime($this->license_to_date),
            'lastListCheck' => self::$addLastCheck ? $this->lastListCheck() : null,
            'currentShift' => self::$addCurrentShift ? $this->currentShift() : null,
            'log' => self::$addLog ? $this->log() : null
        ];
    }
}
