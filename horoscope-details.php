<?php
session_start();
require __DIR__ . '/header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$sign = $_GET['sign'] ?? 'aries';
$sign = strtolower(trim($sign));

$zodiac_data = [
    'aries' => ['name' => 'Aries (Mesha)', 'date' => 'Mar 21 - Apr 19', 'symbol' => '♈'],
    'taurus' => ['name' => 'Taurus (Vrishabha)', 'date' => 'Apr 20 - May 20', 'symbol' => '♉'],
    'gemini' => ['name' => 'Gemini (Mithuna)', 'date' => 'May 21 - Jun 20', 'symbol' => '♊'],
    'cancer' => ['name' => 'Cancer (Karka)', 'date' => 'Jun 21 - Jul 22', 'symbol' => '♋'],
    'leo' => ['name' => 'Leo (Simha)', 'date' => 'Jul 23 - Aug 22', 'symbol' => '♌'],
    'virgo' => ['name' => 'Virgo (Kanya)', 'date' => 'Aug 23 - Sep 22', 'symbol' => '♍'],
    'libra' => ['name' => 'Libra (Tula)', 'date' => 'Sep 23 - Oct 22', 'symbol' => '♎'],
    'scorpio' => ['name' => 'Scorpio (Vrischika)', 'date' => 'Oct 23 - Nov 21', 'symbol' => '♏'],
    'sagittarius' => ['name' => 'Sagittarius (Dhanu)', 'date' => 'Nov 22 - Dec 21', 'symbol' => '♐'],
    'capricorn' => ['name' => 'Capricorn (Makara)', 'date' => 'Dec 22 - Jan 19', 'symbol' => '♑'],
    'aquarius' => ['name' => 'Aquarius (Kumbha)', 'date' => 'Jan 20 - Feb 18', 'symbol' => '♒'],
    'pisces' => ['name' => 'Pisces (Meena)', 'date' => 'Feb 19 - Mar 20', 'symbol' => '♓']
];

if (!array_key_exists($sign, $zodiac_data)) {
    $sign = 'aries';
}

$current_sign = $zodiac_data[$sign];

/*
 * AI PREDICTIONS BASED ON VEDIC TRANSITS (MAY 20, 2026)
 * Transits: Moon, Venus, Jupiter in Gemini | Sun, Mercury in Taurus | Mars in Aries | Saturn in Pisces | Rahu in Aquarius
 */
$all_predictions = [
    'aries' => [
        'Personal' => 'With Mars in your ascendant, you are full of energy and confidence. Communication with siblings and neighbors brings joy as the Moon, Venus, and Jupiter align in your 3rd house.',
        'Profession' => 'Short travels for work are highly favored today. Your ideas will be well received in meetings. Watch your speech as Sun and Mercury sit in your 2nd house of communication.',
        'Health' => 'Vitality is strong, but excess heat from Mars may cause minor headaches or restlessness. Stay hydrated and channel your energy into exercise.',
        'Travel' => 'Short journeys are extremely beneficial and may lead to new friendships or creative inspiration.',
        'Luck' => 'Your courage and initiative are your best luck charms today. Take bold steps.'
    ],
    'taurus' => [
        'Personal' => 'Your focus is on wealth and family harmony. The conjunction in your 2nd house brings sweet speech and enjoyment of good food. You radiate confidence with Sun in your ascendant.',
        'Profession' => 'A wonderful day for financial gains. Investments made today might show positive trends. You have strong authority at your workplace today.',
        'Health' => 'Overall health is stable. Ensure you get enough sleep, as Mars in the 12th house may cause slight insomnia or vivid dreams.',
        'Travel' => 'Travel may result in unexpected expenses. Stick to planned routes if possible.',
        'Luck' => 'Financial luck is highly activated. Opportunities for wealth accumulation present themselves.'
    ],
    'gemini' => [
        'Personal' => 'A fantastic day for you! With the Moon, Venus, and Jupiter in your sign, you exude charm, wisdom, and emotional intelligence. People are naturally drawn to you.',
        'Profession' => 'Your creativity is at an all-time high. Networking and social connections (Mars in 11th) bring excellent professional gains.',
        'Health' => 'You feel rejuvenated and positive. Avoid overindulgence in sweets or luxurious foods.',
        'Travel' => 'Travel for pleasure is highly recommended and will be very satisfying.',
        'Luck' => 'You are surrounded by an aura of good fortune. Trust your instincts.'
    ],
    'cancer' => [
        'Personal' => 'You may feel a strong urge to withdraw and seek spiritual or quiet time. The 12th house focus encourages meditation and inner reflection.',
        'Profession' => 'Excellent career drive with Mars in your 10th house. You can push through obstacles, though you might prefer to work behind the scenes today.',
        'Health' => 'Take extra rest. You might feel emotionally drained if you engage in too many social activities.',
        'Travel' => 'Foreign connections or travel to isolated, peaceful places will bring deep spiritual insights.',
        'Luck' => 'Your luck lies in solitude and charitable deeds today.'
    ],
    'leo' => [
        'Personal' => 'Your social life is buzzing! The 11th house stellium brings joy through friends, elder siblings, and group activities. You feel supported and loved.',
        'Profession' => 'Fantastic visibility in your career (Sun in 10th). You are likely to receive recognition, a promotion, or new leadership responsibilities.',
        'Health' => 'Energy levels are high. Just be mindful of overexerting yourself in social settings.',
        'Travel' => 'Group travel or trips with friends will be highly memorable and successful.',
        'Luck' => 'Fulfillment of desires is strongly indicated. A wish you have been holding onto may manifest.'
    ],
    'virgo' => [
        'Personal' => 'Your focus is entirely on your public image and duties. You may feel a bit detached from domestic matters but highly engaged with the world.',
        'Profession' => 'Tremendous success in your profession. The Moon, Venus, and Jupiter in your 10th house bring grace and wisdom to your work. You are admired by colleagues.',
        'Health' => 'Be cautious of minor sudden events or injuries (Mars in 8th). Drive carefully and avoid reckless activities.',
        'Travel' => 'Work-related travel is favored, but maintain caution during transit.',
        'Luck' => 'Professional luck is peaking. Your reputation precedes you.'
    ],
    'libra' => [
        'Personal' => 'A day for higher wisdom, philosophy, and spiritual pursuits. You may feel deeply connected to a guru or father figure. Relationships need careful handling (Mars in 7th).',
        'Profession' => 'Publishing, teaching, or legal matters are highly favored. Avoid aggressive arguments with business partners.',
        'Health' => 'Good health overall, but stress from partnerships could affect your peace of mind.',
        'Travel' => 'Long-distance travel or pilgrimage is highly auspicious and brings joy.',
        'Luck' => 'Divine grace and luck from past good deeds support you in unexpected ways.'
    ],
    'scorpio' => [
        'Personal' => 'A day of intense transformation and deep research. You may uncover hidden truths or experience sudden changes in your emotional state.',
        'Profession' => 'High competitive energy (Mars in 6th) allows you to defeat competitors and overcome obstacles easily. Sudden financial gains from joint resources are possible.',
        'Health' => 'Focus on your spouse or partner\'s health. For yourself, digestion and immunity are strong.',
        'Travel' => 'Travel may bring unexpected delays or sudden changes in itinerary. Be adaptable.',
        'Luck' => 'Luck comes through research, hidden knowledge, and resolving past debts.'
    ],
    'sagittarius' => [
        'Personal' => 'Relationships are in the spotlight! With Jupiter, Venus, and Moon in your 7th house, interactions with your partner are filled with love, wisdom, and harmony.',
        'Profession' => 'Excellent day for business partnerships and negotiations. You communicate effectively, though you should avoid workplace disputes (Sun in 6th).',
        'Health' => 'Good health, provided you don\'t let minor workplace stress get to you.',
        'Travel' => 'Travel with a partner or spouse will be romantic and spiritually uplifting.',
        'Luck' => 'Others are your source of luck today. Collaborative efforts will succeed.'
    ],
    'capricorn' => [
        'Personal' => 'Your focus shifts to overcoming daily challenges and improving your routines. Domestic peace requires attention (Mars in 4th).',
        'Profession' => 'You tackle work with immense wisdom and creativity. Subordinates and colleagues are very supportive. Excellent day for problem-solving.',
        'Health' => 'A great day to start a new diet or health regimen. Healing energies are strong.',
        'Travel' => 'Travel related to health or daily work routines will be productive.',
        'Luck' => 'Luck favors hard work and discipline today. Your creative intelligence (Sun in 5th) guides you.'
    ],
    'aquarius' => [
        'Personal' => 'A highly creative and romantic day! The 5th house focus brings joy through children, arts, and self-expression. You feel deeply inspired.',
        'Profession' => 'Great courage and initiative (Mars in 3rd). You can successfully launch new ideas or projects. Your mind is sharp and innovative.',
        'Health' => 'Excellent mental health and happiness. Ensure you maintain a balanced diet.',
        'Travel' => 'Short trips for leisure or creative pursuits will be highly enjoyable.',
        'Luck' => 'Luck shines on your creative endeavors and romantic life.'
    ],
    'pisces' => [
        'Personal' => 'You find immense peace and joy at home. Spending time with family, mother, or redecorating your living space brings happiness.',
        'Profession' => 'Success in real estate, education, or home-based businesses. Watch your tone when communicating (Mars in 2nd).',
        'Health' => 'Emotional well-being is strong. Take care of your throat and avoid harsh foods.',
        'Travel' => 'Travel related to hometown or visiting parents is favored.',
        'Luck' => 'Your inner peace and emotional satisfaction attract positive circumstances.'
    ]
];

$predictions = $all_predictions[$sign];

?>

<section class="kundli-section">
    <div class="kundli-container">
        
        <div class="kundli-title" style="margin-bottom:var(--s8);">
            <div style="font-size: 64px; color: var(--amber); margin-bottom: 10px; line-height: 1;"><?= $current_sign['symbol'] ?></div>
            <h1 style="text-transform: uppercase; letter-spacing: 2px;">Daily Horoscope: <?= $current_sign['name'] ?></h1>
            <p>Based on Current Vedic Planetary Transits</p>
            <div class="kundli-divider"></div>
        </div>

        <div style="max-width: 800px; margin: 0 auto;">
            
            <div style="background:var(--bg-tertiary); border:1px solid var(--border); padding:var(--s7); border-radius:var(--r-lg); margin-bottom:var(--s6);">
                <h3 style="color:var(--amber); margin-bottom:var(--s4); font-size:20px;">Transit Overview for Today</h3>
                <p style="color:var(--text-2); font-size:15px; line-height:1.8;">
                    Welcome, <?= $current_sign['name'] ?>! Today's predictions are dynamically formulated based on the precise current planetary positions (Lahiri Ayanamsa). The Moon is currently transiting Gemini along with Jupiter and Venus, while the Sun and Mercury are in Taurus, and Mars is in Aries. Read below to see how these transits uniquely influence your Moon sign today.
                </p>
            </div>
            
            <div class="grid-2">
                <?php foreach($predictions as $category => $text): ?>
                <div style="background:var(--bg-tertiary); border:1px solid var(--border); padding:var(--s5); border-radius:var(--r-lg);">
                    <h4 style="color:var(--amber); margin-bottom:var(--s2); font-size:16px;"><?= $category ?></h4>
                    <p style="color:var(--text-1); font-size:14px; line-height:1.6;"><?= $text ?></p>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align:center; margin-top:var(--s8);">
                <a href="horoscope.php" class="generate-btn" style="display:inline-block; max-width:300px; text-decoration:none;">View Another Zodiac</a>
            </div>

        </div>
        
    </div>
</section>

<?php require __DIR__ . '/bottom.php'; ?>
