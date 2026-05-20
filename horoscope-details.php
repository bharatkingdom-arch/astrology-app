<?php
session_start();
require __DIR__ . '/header.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

$sign = $_GET['sign'] ?? 'aries';
$sign = strtolower(trim($sign));

$lang = $_GET['lang'] ?? 'en';
if ($lang !== 'en' && $lang !== 'te') {
    $lang = 'en';
}

$zodiac_data = [
    'aries' => ['name_en' => 'Aries (Mesha)', 'name_te' => 'మేష రాశి (Aries)', 'date' => 'Mar 21 - Apr 19', 'symbol' => '♈'],
    'taurus' => ['name_en' => 'Taurus (Vrishabha)', 'name_te' => 'వృషభ రాశి (Taurus)', 'date' => 'Apr 20 - May 20', 'symbol' => '♉'],
    'gemini' => ['name_en' => 'Gemini (Mithuna)', 'name_te' => 'మిథున రాశి (Gemini)', 'date' => 'May 21 - Jun 20', 'symbol' => '♊'],
    'cancer' => ['name_en' => 'Cancer (Karka)', 'name_te' => 'కర్కాటక రాశి (Cancer)', 'date' => 'Jun 21 - Jul 22', 'symbol' => '♋'],
    'leo' => ['name_en' => 'Leo (Simha)', 'name_te' => 'సింహ రాశి (Leo)', 'date' => 'Jul 23 - Aug 22', 'symbol' => '♌'],
    'virgo' => ['name_en' => 'Virgo (Kanya)', 'name_te' => 'కన్య రాశి (Virgo)', 'date' => 'Aug 23 - Sep 22', 'symbol' => '♍'],
    'libra' => ['name_en' => 'Libra (Tula)', 'name_te' => 'తులా రాశి (Libra)', 'date' => 'Sep 23 - Oct 22', 'symbol' => '♎'],
    'scorpio' => ['name_en' => 'Scorpio (Vrischika)', 'name_te' => 'వృశ్చిక రాశి (Scorpio)', 'date' => 'Oct 23 - Nov 21', 'symbol' => '♏'],
    'sagittarius' => ['name_en' => 'Sagittarius (Dhanu)', 'name_te' => 'ధనుస్సు రాశి (Sagittarius)', 'date' => 'Nov 22 - Dec 21', 'symbol' => '♐'],
    'capricorn' => ['name_en' => 'Capricorn (Makara)', 'name_te' => 'మకర రాశి (Capricorn)', 'date' => 'Dec 22 - Jan 19', 'symbol' => '♑'],
    'aquarius' => ['name_en' => 'Aquarius (Kumbha)', 'name_te' => 'కుంభ రాశి (Aquarius)', 'date' => 'Jan 20 - Feb 18', 'symbol' => '♒'],
    'pisces' => ['name_en' => 'Pisces (Meena)', 'name_te' => 'మీన రాశి (Pisces)', 'date' => 'Feb 19 - Mar 20', 'symbol' => '♓']
];

if (!array_key_exists($sign, $zodiac_data)) {
    $sign = 'aries';
}

$current_sign = $zodiac_data[$sign];
$display_name = ($lang === 'te') ? $current_sign['name_te'] : $current_sign['name_en'];

/*
 * AI PREDICTIONS BASED ON VEDIC TRANSITS
 * English and Telugu Translations
 */
$all_predictions = [
    'aries' => [
        'en' => [
            'Personal' => 'With Mars in your ascendant, you are full of energy and confidence. Communication with siblings and neighbors brings joy as the Moon, Venus, and Jupiter align in your 3rd house.',
            'Profession' => 'Short travels for work are highly favored today. Your ideas will be well received in meetings. Watch your speech as Sun and Mercury sit in your 2nd house of communication.',
            'Health' => 'Vitality is strong, but excess heat from Mars may cause minor headaches or restlessness. Stay hydrated and channel your energy into exercise.',
            'Travel' => 'Short journeys are extremely beneficial and may lead to new friendships or creative inspiration.',
            'Luck' => 'Your courage and initiative are your best luck charms today. Take bold steps.'
        ],
        'te' => [
            'వ్యక్తిగత జీవితం' => 'మీ లగ్నంలో కుజుడు ఉండటం వల్ల, మీరు శక్తి మరియు ఆత్మవిశ్వాసంతో నిండి ఉంటారు. చంద్రుడు, శుక్రుడు మరియు గురుడు మీ 3వ ఇంట్లో ఉండటం వలన తోబుట్టువులు మరియు ఇరుగుపొరుగు వారితో మంచి సంబంధాలు ఉంటాయి.',
            'వృత్తి' => 'పని కోసం చిన్న ప్రయాణాలు అనుకూలంగా ఉన్నాయి. సమావేశాలలో మీ ఆలోచనలకు మంచి స్పందన లభిస్తుంది. అయితే మాటల విషయంలో జాగ్రత్త.',
            'ఆరోగ్యం' => 'మీ ఆరోగ్యం బలంగా ఉంటుంది, కానీ కుజుడి వేడి వల్ల చిన్న తలనొప్పులు రావచ్చు. మంచి వ్యాయామం అవసరం.',
            'ప్రయాణం' => 'చిన్న ప్రయాణాలు చాలా లాభదాయకంగా ఉంటాయి.',
            'అదృష్టం' => 'మీ ధైర్యం మరియు చొరవే నేడు మీ అదృష్టం.'
        ]
    ],
    'taurus' => [
        'en' => [
            'Personal' => 'Your focus is on wealth and family harmony. The conjunction in your 2nd house brings sweet speech and enjoyment of good food. You radiate confidence with Sun in your ascendant.',
            'Profession' => 'A wonderful day for financial gains. Investments made today might show positive trends. You have strong authority at your workplace today.',
            'Health' => 'Overall health is stable. Ensure you get enough sleep, as Mars in the 12th house may cause slight insomnia or vivid dreams.',
            'Travel' => 'Travel may result in unexpected expenses. Stick to planned routes if possible.',
            'Luck' => 'Financial luck is highly activated. Opportunities for wealth accumulation present themselves.'
        ],
        'te' => [
            'వ్యక్తిగత జీవితం' => 'మీ దృష్టి సంపద మరియు కుటుంబ సామరస్యంపై ఉంటుంది. 2వ ఇంట్లో గ్రహాల కలయిక వల్ల మీ మాట తీరు బాగుంటుంది మరియు మంచి భోజనం ఆస్వాదిస్తారు.',
            'వృత్తి' => 'ఆర్థిక లాభాలకు అద్భుతమైన రోజు. మీరు చేసిన పెట్టుబడులు సానుకూల ఫలితాలను ఇస్తాయి.',
            'ఆరోగ్యం' => 'మొత్తం ఆరోగ్యం స్థిరంగా ఉంటుంది. 12వ ఇంట్లో కుజుడు వల్ల నిద్రలేమి లేదా కలలు రావచ్చు, కాబట్టి తగినంత నిద్ర ఉండేలా చూసుకోండి.',
            'ప్రయాణం' => 'ప్రయాణాల వల్ల అనుకోని ఖర్చులు రావచ్చు. సాధ్యమైనంత వరకు ప్రణాళిక ప్రకారం నడుచుకోండి.',
            'అదృష్టం' => 'ఆర్థిక అదృష్టం చాలా ఎక్కువగా ఉంది.'
        ]
    ],
    'gemini' => [
        'en' => [
            'Personal' => 'A fantastic day for you! With the Moon, Venus, and Jupiter in your sign, you exude charm, wisdom, and emotional intelligence. People are naturally drawn to you.',
            'Profession' => 'Your creativity is at an all-time high. Networking and social connections (Mars in 11th) bring excellent professional gains.',
            'Health' => 'You feel rejuvenated and positive. Avoid overindulgence in sweets or luxurious foods.',
            'Travel' => 'Travel for pleasure is highly recommended and will be very satisfying.',
            'Luck' => 'You are surrounded by an aura of good fortune. Trust your instincts.'
        ],
        'te' => [
            'వ్యక్తిగత జీవితం' => 'మీకు అద్భుతమైన రోజు! మీ రాశిలో చంద్రుడు, శుక్రుడు, గురుడు ఉండటం వల్ల మీరు ఆకర్షణీయంగా మరియు తెలివిగా వ్యవహరిస్తారు. జనాలు మీ పట్ల ఆకర్షితులవుతారు.',
            'వృత్తి' => 'మీ సృజనాత్మకత గరిష్ట స్థాయికి చేరుకుంటుంది. 11వ ఇంట్లో కుజుడి వల్ల సామాజిక పరిచయాలు వృత్తిపరంగా లాభిస్తాయి.',
            'ఆరోగ్యం' => 'మీరు ఉత్సాహంగా, సానుకూలంగా ఉంటారు. స్వీట్లు ఎక్కువగా తినడం మానుకోండి.',
            'ప్రయాణం' => 'విహారయాత్రలు అత్యంత సంతృప్తికరంగా ఉంటాయి.',
            'అదృష్టం' => 'మీరు మంచి అదృష్టంతో ఉన్నారు. మీ అంతర్గత గొంతును నమ్మండి.'
        ]
    ],
    'cancer' => [
        'en' => [
            'Personal' => 'You may feel a strong urge to withdraw and seek spiritual or quiet time. The 12th house focus encourages meditation and inner reflection.',
            'Profession' => 'Excellent career drive with Mars in your 10th house. You can push through obstacles, though you might prefer to work behind the scenes today.',
            'Health' => 'Take extra rest. You might feel emotionally drained if you engage in too many social activities.',
            'Travel' => 'Foreign connections or travel to isolated, peaceful places will bring deep spiritual insights.',
            'Luck' => 'Your luck lies in solitude and charitable deeds today.'
        ],
        'te' => [
            'వ్యక్తిగత జీవితం' => 'ఆధ్యాత్మిక విషయాలపై మరియు ఒంటరిగా గడపడంపై మీ ఆసక్తి పెరుగుతుంది. 12వ ఇంటి ప్రభావం మిమ్మల్ని ధ్యానం వైపు నడిపిస్తుంది.',
            'వృత్తి' => '10వ ఇంట్లో కుజుడి వల్ల వృత్తిలో మంచి పట్టుదల ఉంటుంది. అడ్డంకులను సులభంగా దాటగలరు, కానీ మీరు వెనుక ఉండి పని చేయడానికి ఇష్టపడతారు.',
            'ఆరోగ్యం' => 'తగినంత విశ్రాంతి తీసుకోండి. సామాజిక కార్యక్రమాల్లో ఎక్కువగా పాల్గొనడం వల్ల అలసిపోవచ్చు.',
            'ప్రయాణం' => 'విదేశీ ప్రయాణాలు లేదా ప్రశాంత ప్రదేశాలకు వెళ్లడం మానసిక ప్రశాంతతను ఇస్తుంది.',
            'అదృష్టం' => 'ఈ రోజు ఒంటరితనం మరియు దానధర్మాలు చేయడం వల్ల మీకు అదృష్టం కలుగుతుంది.'
        ]
    ],
    'leo' => [
        'en' => [
            'Personal' => 'Your social life is buzzing! The 11th house stellium brings joy through friends, elder siblings, and group activities. You feel supported and loved.',
            'Profession' => 'Fantastic visibility in your career (Sun in 10th). You are likely to receive recognition, a promotion, or new leadership responsibilities.',
            'Health' => 'Energy levels are high. Just be mindful of overexerting yourself in social settings.',
            'Travel' => 'Group travel or trips with friends will be highly memorable and successful.',
            'Luck' => 'Fulfillment of desires is strongly indicated. A wish you have been holding onto may manifest.'
        ],
        'te' => [
            'వ్యక్తిగత జీవితం' => 'మీ సామాజిక జీవితం సందడిగా ఉంటుంది! 11వ ఇంట్లో గ్రహాల వల్ల స్నేహితులు మరియు సమూహ కార్యక్రమాల ద్వారా సంతోషం లభిస్తుంది.',
            'వృత్తి' => 'వృత్తిలో గొప్ప గుర్తింపు (10వ ఇంట్లో సూర్యుడు). ప్రమోషన్ లేదా కొత్త నాయకత్వ బాధ్యతలు మీ ముందుకు రావచ్చు.',
            'ఆరోగ్యం' => 'శక్తి స్థాయిలు ఎక్కువగా ఉంటాయి. సామాజిక కార్యక్రమాల్లో అతిగా శ్రమించకుండా చూసుకోండి.',
            'ప్రయాణం' => 'స్నేహితులతో కలిసి చేసే సమూహ ప్రయాణాలు చాలా ఆనందదాయకంగా ఉంటాయి.',
            'అదృష్టం' => 'కోరికలు నెరవేరుతాయి. మీరు ఎప్పటినుంచో కోరుకున్నది జరగవచ్చు.'
        ]
    ],
    'virgo' => [
        'en' => [
            'Personal' => 'Your focus is entirely on your public image and duties. You may feel a bit detached from domestic matters but highly engaged with the world.',
            'Profession' => 'Tremendous success in your profession. The Moon, Venus, and Jupiter in your 10th house bring grace and wisdom to your work. You are admired by colleagues.',
            'Health' => 'Be cautious of minor sudden events or injuries (Mars in 8th). Drive carefully and avoid reckless activities.',
            'Travel' => 'Work-related travel is favored, but maintain caution during transit.',
            'Luck' => 'Professional luck is peaking. Your reputation precedes you.'
        ],
        'te' => [
            'వ్యక్తిగత జీవితం' => 'మీ దృష్టి పూర్తిగా మీ సామాజిక హోదా మరియు విధుల్లో ఉంటుంది. కుటుంబ విషయాల నుండి కాస్త దూరంగా ఉన్నట్లు అనిపించినా, బాధ్యతగా ఉంటారు.',
            'వృత్తి' => 'మీ వృత్తిలో అద్భుతమైన విజయం. 10వ ఇంట్లో చంద్రుడు, శుక్రుడు, గురుడు మీ పనిలో తెలివిని మరియు నైపుణ్యాన్ని ఇస్తారు.',
            'ఆరోగ్యం' => '8వ ఇంట్లో కుజుడి వల్ల చిన్నపాటి ప్రమాదాలు లేదా గాయాలు జరిగే అవకాశం ఉంది. డ్రైవింగ్ చేసేటప్పుడు జాగ్రత్త అవసరం.',
            'ప్రయాణం' => 'పనికి సంబంధించిన ప్రయాణాలకు అనుకూలం, కానీ జాగ్రత్త వహించండి.',
            'అదృష్టం' => 'వృత్తిపరమైన అదృష్టం గరిష్ట స్థాయికి చేరుకుంటుంది.'
        ]
    ],
    'libra' => [
        'en' => [
            'Personal' => 'A day for higher wisdom, philosophy, and spiritual pursuits. You may feel deeply connected to a guru or father figure. Relationships need careful handling (Mars in 7th).',
            'Profession' => 'Publishing, teaching, or legal matters are highly favored. Avoid aggressive arguments with business partners.',
            'Health' => 'Good health overall, but stress from partnerships could affect your peace of mind.',
            'Travel' => 'Long-distance travel or pilgrimage is highly auspicious and brings joy.',
            'Luck' => 'Divine grace and luck from past good deeds support you in unexpected ways.'
        ],
        'te' => [
            'వ్యక్తిగత జీవితం' => 'ఉన్నత జ్ఞానం మరియు ఆధ్యాత్మిక సాధన కోసం అద్భుతమైన రోజు. మీ తండ్రి లేదా గురువుతో అనుబంధం పెరుగుతుంది. భాగస్వామ్య సంబంధాలలో జాగ్రత్త అవసరం (7వ ఇంట్లో కుజుడు).',
            'వృత్తి' => 'ప్రచురణ, బోధన లేదా చట్టపరమైన విషయాలకు అనుకూలమైన రోజు. వ్యాపార భాగస్వాములతో గొడవలకు దూరంగా ఉండండి.',
            'ఆరోగ్యం' => 'ఆరోగ్యం బాగుంటుంది, కానీ భాగస్వామ్యాల వల్ల ఒత్తిడి కలుగవచ్చు.',
            'ప్రయాణం' => 'సుదూర ప్రయాణాలు లేదా తీర్థయాత్రలు సంతోషాన్ని ఇస్తాయి.',
            'అదృష్టం' => 'దైవానుగ్రహం మరియు గత జన్మ పుణ్యం మిమ్మల్ని కాపాడుతాయి.'
        ]
    ],
    'scorpio' => [
        'en' => [
            'Personal' => 'A day of intense transformation and deep research. You may uncover hidden truths or experience sudden changes in your emotional state.',
            'Profession' => 'High competitive energy (Mars in 6th) allows you to defeat competitors and overcome obstacles easily. Sudden financial gains from joint resources are possible.',
            'Health' => 'Focus on your spouse or partner\'s health. For yourself, digestion and immunity are strong.',
            'Travel' => 'Travel may bring unexpected delays or sudden changes in itinerary. Be adaptable.',
            'Luck' => 'Luck comes through research, hidden knowledge, and resolving past debts.'
        ],
        'te' => [
            'వ్యక్తిగత జీవితం' => 'లోతైన పరిశోధన మరియు భావోద్వేగ మార్పులకు అవకాశం ఉంది. మీరు కొన్ని దాచిన నిజాలను తెలుసుకుంటారు.',
            'వృత్తి' => '6వ ఇంట్లో కుజుడి వల్ల పోటీలో విజయం సాధిస్తారు. జాయింట్ ఆస్తుల నుండి అకస్మాత్తుగా ఆర్థిక లాభాలు రావచ్చు.',
            'ఆరోగ్యం' => 'జీవిత భాగస్వామి ఆరోగ్యంపై శ్రద్ధ వహించండి. మీ జీర్ణశక్తి మరియు రోగనిరోధక శక్తి బలంగా ఉంటాయి.',
            'ప్రయాణం' => 'ప్రయాణాల్లో ప్రణాళికలు మారవచ్చు. దానికి అనుగుణంగా సర్దుకుపోవాలి.',
            'అదృష్టం' => 'పరిశోధన మరియు పాత బకాయిలు తీర్చడం ద్వారా అదృష్టం వరిస్తుంది.'
        ]
    ],
    'sagittarius' => [
        'en' => [
            'Personal' => 'Relationships are in the spotlight! With Jupiter, Venus, and Moon in your 7th house, interactions with your partner are filled with love, wisdom, and harmony.',
            'Profession' => 'Excellent day for business partnerships and negotiations. You communicate effectively, though you should avoid workplace disputes (Sun in 6th).',
            'Health' => 'Good health, provided you don\'t let minor workplace stress get to you.',
            'Travel' => 'Travel with a partner or spouse will be romantic and spiritually uplifting.',
            'Luck' => 'Others are your source of luck today. Collaborative efforts will succeed.'
        ],
        'te' => [
            'వ్యక్తిగత జీవితం' => 'సంబంధాలు ప్రధానంగా నిలుస్తాయి! 7వ ఇంట్లో గురుడు, శుక్రుడు, చంద్రుడు ఉండటం వల్ల భాగస్వామితో మంచి అనుబంధం, ప్రేమ మరియు సామరస్యం ఉంటాయి.',
            'వృత్తి' => 'వ్యాపార భాగస్వామ్యాలకు మరియు చర్చలకు అద్భుతమైన రోజు. పనిలో వివాదాలకు (6వ ఇంట్లో సూర్యుడు) దూరంగా ఉండండి.',
            'ఆరోగ్యం' => 'పని ఒత్తిడిని మీ దరిచేరనీయకపోతే ఆరోగ్యం బాగుంటుంది.',
            'ప్రయాణం' => 'జీవిత భాగస్వామితో చేసే ప్రయాణాలు ఆధ్యాత్మికంగా ఉత్తేజాన్ని ఇస్తాయి.',
            'అదృష్టం' => 'ఇతరులతో కలిసి చేసే పనుల వల్ల మీకు అదృష్టం కలిసివస్తుంది.'
        ]
    ],
    'capricorn' => [
        'en' => [
            'Personal' => 'Your focus shifts to overcoming daily challenges and improving your routines. Domestic peace requires attention (Mars in 4th).',
            'Profession' => 'You tackle work with immense wisdom and creativity. Subordinates and colleagues are very supportive. Excellent day for problem-solving.',
            'Health' => 'A great day to start a new diet or health regimen. Healing energies are strong.',
            'Travel' => 'Travel related to health or daily work routines will be productive.',
            'Luck' => 'Luck favors hard work and discipline today. Your creative intelligence (Sun in 5th) guides you.'
        ],
        'te' => [
            'వ్యక్తిగత జీవితం' => 'రోజువారీ సవాళ్లను అధిగమించడం మరియు దినచర్యను మెరుగుపరచుకోవడంపై మీ దృష్టి ఉంటుంది. ఇంట్లో ప్రశాంతత కోసం ప్రయత్నించాలి (4వ ఇంట్లో కుజుడు).',
            'వృత్తి' => 'సృజనాత్మకతతో మీ పనిని సులభతరం చేస్తారు. సహోద్యోగులు చాలా మద్దతు ఇస్తారు. సమస్యలను పరిష్కరించడానికి ఇది మంచి రోజు.',
            'ఆరోగ్యం' => 'కొత్త డైట్ లేదా వ్యాయామం ప్రారంభించడానికి అనుకూలమైన రోజు.',
            'ప్రయాణం' => 'ఆరోగ్య పరంగా లేదా రోజువారీ పనికి సంబంధించిన ప్రయాణాలు ఫలితాన్నిస్తాయి.',
            'అదృష్టం' => 'కష్టపడి పనిచేయడం వల్ల అదృష్టం కలుగుతుంది. మీ సృజనాత్మక తెలివితేటలు మార్గనిర్దేశం చేస్తాయి.'
        ]
    ],
    'aquarius' => [
        'en' => [
            'Personal' => 'A highly creative and romantic day! The 5th house focus brings joy through children, arts, and self-expression. You feel deeply inspired.',
            'Profession' => 'Great courage and initiative (Mars in 3rd). You can successfully launch new ideas or projects. Your mind is sharp and innovative.',
            'Health' => 'Excellent mental health and happiness. Ensure you maintain a balanced diet.',
            'Travel' => 'Short trips for leisure or creative pursuits will be highly enjoyable.',
            'Luck' => 'Luck shines on your creative endeavors and romantic life.'
        ],
        'te' => [
            'వ్యక్తిగత జీవితం' => 'అత్యంత సృజనాత్మక మరియు శృంగారభరితమైన రోజు! 5వ ఇంటి ప్రభావం వల్ల పిల్లలు మరియు కళల ద్వారా ఆనందం కలుగుతుంది.',
            'వృత్తి' => 'గొప్ప ధైర్యం మరియు చొరవ తీసుకుంటారు (3వ ఇంట్లో కుజుడు). కొత్త ఆలోచనలను విజయవంతంగా అమలు చేయగలరు. మీ మనస్సు చాలా చురుకుగా ఉంటుంది.',
            'ఆరోగ్యం' => 'మానసిక ఆరోగ్యం మరియు సంతోషం అద్భుతంగా ఉంటాయి. పోషకాహారం తీసుకోవాలి.',
            'ప్రయాణం' => 'వినోదం లేదా సృజనాత్మక పనుల కోసం చేసే చిన్న ప్రయాణాలు చాలా ఆనందాన్ని ఇస్తాయి.',
            'అదృష్టం' => 'మీ సృజనాత్మక పనులలో మరియు ప్రేమ జీవితంలో అదృష్టం మెరుస్తుంది.'
        ]
    ],
    'pisces' => [
        'en' => [
            'Personal' => 'You find immense peace and joy at home. Spending time with family, mother, or redecorating your living space brings happiness.',
            'Profession' => 'Success in real estate, education, or home-based businesses. Watch your tone when communicating (Mars in 2nd).',
            'Health' => 'Emotional well-being is strong. Take care of your throat and avoid harsh foods.',
            'Travel' => 'Travel related to hometown or visiting parents is favored.',
            'Luck' => 'Your inner peace and emotional satisfaction attract positive circumstances.'
        ],
        'te' => [
            'వ్యక్తిగత జీవితం' => 'ఇంట్లో అపారమైన ప్రశాంతత మరియు ఆనందాన్ని పొందుతారు. కుటుంబంతో గడపడం లేదా మీ ఇంటిని అందంగా అలంకరించడం సంతోషాన్నిస్తుంది.',
            'వృత్తి' => 'రియల్ ఎస్టేట్, విద్య లేదా ఇంటి ఆధారిత వ్యాపారాలలో విజయం. మాట్లాడేటప్పుడు మీ స్వరాన్ని అదుపులో ఉంచుకోండి (2వ ఇంట్లో కుజుడు).',
            'ఆరోగ్యం' => 'భావోద్వేగ ఆరోగ్యం బలంగా ఉంటుంది. గొంతును జాగ్రత్తగా చూసుకోండి మరియు కారంగా ఉండే ఆహారాలకు దూరంగా ఉండండి.',
            'ప్రయాణం' => 'సొంత ఊరు లేదా తల్లిదండ్రులను సందర్శించడానికి ప్రయాణాలు అనుకూలం.',
            'అదృష్టం' => 'మీ మానసిక ప్రశాంతత సానుకూల పరిస్థితులను ఆకర్షిస్తుంది.'
        ]
    ]
];

$predictions = $all_predictions[$sign][$lang];

// Static text translations
$static = [
    'en' => [
        'daily_horoscope' => 'Daily Horoscope',
        'based_on' => 'Based on Current Vedic Planetary Transits',
        'overview_title' => 'Transit Overview for Today',
        'overview_desc' => "Welcome, {$display_name}! Today's predictions are dynamically formulated based on the precise current planetary positions (Lahiri Ayanamsa). The Moon is currently transiting Gemini along with Jupiter and Venus, while the Sun and Mercury are in Taurus, and Mars is in Aries. Read below to see how these transits uniquely influence your Moon sign today.",
        'view_another' => 'View Another Zodiac',
        'switch_lang' => 'తెలుగులో చదవండి (Read in Telugu)'
    ],
    'te' => [
        'daily_horoscope' => 'నేటి రాశి ఫలాలు',
        'based_on' => 'ప్రస్తుత వేద గ్రహ సంచారాల ఆధారంగా',
        'overview_title' => 'నేటి గ్రహ సంచారాల అవలోకనం',
        'overview_desc' => "స్వాగతం, {$display_name}! నేటి రాశి ఫలాలు ఖచ్చితమైన ప్రస్తుత గ్రహ స్థానాల (లాహిరి అయనాంశ) ఆధారంగా రూపొందించబడ్డాయి. చంద్రుడు, గురుడు మరియు శుక్రుడితో కలిసి మిథునరాశిలో సంచరిస్తున్నాడు. సూర్యుడు, బుధుడు వృషభరాశిలో, కుజుడు మేషరాశిలో ఉన్నాడు. ఈ గ్రహ సంచారాలు ఈరోజు మీ రాశిపై ఎలాంటి ప్రభావం చూపుతాయో కింద చదవండి.",
        'view_another' => 'మరొక రాశిని చూడండి',
        'switch_lang' => 'Read in English'
    ]
];

$t = $static[$lang];
$toggle_lang = ($lang === 'en') ? 'te' : 'en';

?>

<section class="kundli-section">
    <div class="kundli-container">
        
        <div class="kundli-title" style="margin-bottom:var(--s8);">
            
            <div style="text-align: right; margin-bottom: 20px;">
                <a href="?sign=<?= $sign ?>&lang=<?= $toggle_lang ?>" style="display: inline-block; padding: 6px 12px; border-radius: 20px; border: 1px solid var(--amber); color: var(--amber); font-size: 13px; text-decoration: none;">
                    <?= $t['switch_lang'] ?>
                </a>
            </div>

            <div style="font-size: 64px; color: var(--amber); margin-bottom: 10px; line-height: 1;"><?= $current_sign['symbol'] ?></div>
            <h1 style="text-transform: uppercase; letter-spacing: 2px;"><?= $t['daily_horoscope'] ?>: <?= $display_name ?></h1>
            <p><?= $t['based_on'] ?></p>
            <div class="kundli-divider"></div>
        </div>

        <div style="max-width: 800px; margin: 0 auto;">
            
            <div style="background:var(--bg-tertiary); border:1px solid var(--border); padding:var(--s7); border-radius:var(--r-lg); margin-bottom:var(--s6);">
                <h3 style="color:var(--amber); margin-bottom:var(--s4); font-size:20px;"><?= $t['overview_title'] ?></h3>
                <p style="color:var(--text-2); font-size:15px; line-height:1.8;">
                    <?= $t['overview_desc'] ?>
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
                <a href="horoscope.php" class="generate-btn" style="display:inline-block; max-width:300px; text-decoration:none;"><?= $t['view_another'] ?></a>
            </div>

        </div>
        
    </div>
</section>

<?php require __DIR__ . '/bottom.php'; ?>
