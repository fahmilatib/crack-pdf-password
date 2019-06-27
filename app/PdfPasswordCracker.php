<?php

// Credit: http://blog.rubypdf.com/pdfcrack/

// Only works for yyyymmdd password format

namespace App;

use Carbon\Carbon;

class PdfPasswordCracker
{
    /**
     * @var string
     */
    protected const PASSWORDS_FILE = __DIR__ . '/../passwords';

    /**
     * @var string
     */
    protected const PDF_FILE = __DIR__ . '/../a.pdf';

    /**
     * @var string
     */
    protected $password;

    /**
     * @var int
     */
    protected $minAge;

    /**
     * @var int
     */
    protected $maxAge;

    /**
     * @var array
     */
    protected $dates = [];

    /**
     * @param  int $minAge
     * @return $this
     */
    public function minAge($minAge)
    {
        $this->minAge = $minAge;
        return $this;
    }

    /**
     * @param  int $maxAge
     * @return $this
     */
    public function maxAge($maxAge)
    {
        $this->maxAge = $maxAge;
        return $this;
    }

    /**
     * @return bool
     */
    public function crack()
    {
        $currentYear = Carbon::now()->year;
        $minBirthYear = $currentYear - $this->maxAge;
        $minBirthDate = Carbon::create($minBirthYear);
        $maxBirthYear = $currentYear - $this->minAge;
        $maxBirthDate = Carbon::create($maxBirthYear)->endOfYear()->startOfDay();

        while ($minBirthDate->lessThanOrEqualTo($maxBirthDate)) {
            $this->dates[] = $minBirthDate->format('Ymd');
            $minBirthDate->addDay();
        }

        $this->createPasswordsFile();

        return $this->passwordFound(exec(__DIR__ . '/../pdfcrack-0.11/pdfcrack.exe -f ' . static::PDF_FILE . ' -w ' . static::PASSWORDS_FILE));
    }

    /**
     * @return void
     */
    protected function createPasswordsFile()
    {
        $handle = fopen(static::PASSWORDS_FILE, 'w');

        foreach ($this->dates as $date) {
            fwrite($handle, $date . PHP_EOL);
        }

        fclose($handle);
    }

    /**
     * @param  string $output
     * @return bool
     */
    protected function passwordFound($output)
    {
        // found user-password: '20010101'

        if (mb_strpos($output, 'found') === false) {
            return false;
        }

        $this->password = mb_substr($output, -9, 8);

        return true;
    }

    /**
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }
}
