<?php

namespace App\Helpers\Utils;

class AudioPath
{
    //Base Audio Path
    const baseAudioPath = 'audios' . DIRECTORY_SEPARATOR;

    //News
    const baseSocietyPath = self::baseAudioPath . 'society' . DIRECTORY_SEPARATOR;
    const newsAudio = self::baseSocietyPath . 'news';
}
