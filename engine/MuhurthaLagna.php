
<?php
/**
 * Muhurtha Lagna Engine
 * Rule:
 * - Starts from PR sign at Sunrise
 * - Every 4 minutes → Next Sign
 * - Resets at next Sunrise
 */

class MuhurthaLagna
{
    private $signs = [
        "Aries","Taurus","Gemini","Cancer","Leo","Virgo",
        "Libra","Scorpio","Sagittarius","Capricorn","Aquarius","Pisces"
    ];

    /**
     * Get Muhurtha Lagna Sign
     *
     * @param float  $prLongitude       PR longitude (0–360)
     * @param string $sunriseDateTime   Sunrise datetime (Y-m-d H:i:s)
     * @param string $currentDateTime   Current datetime (Y-m-d H:i:s)
     * @return string
     */
    public function getMLSign($prLongitude, $sunriseDateTime, $currentDateTime)
    {
        // 1️⃣ Find PR Sign Index
        $prSignIndex = floor($prLongitude / 30);

        $current = strtotime($currentDateTime);
        $sunrise = strtotime($sunriseDateTime);

        // 2️⃣ If current time is before sunrise,
        // use previous day's sunrise
        if ($current < $sunrise) {
            $sunrise = strtotime("-1 day", $sunrise);
        }

        // 3️⃣ Minutes since last sunrise
        $minutesPassed = ($current - $sunrise) / 60;

        // 4️⃣ Each 4 minutes = next sign
        $shift = floor($minutesPassed / 4);

        // 5️⃣ Final ML index
        $mlIndex = ($prSignIndex + $shift) % 12;

        if ($mlIndex < 0) {
            $mlIndex += 12;
        }

        return $this->signs[$mlIndex];
    }
}
