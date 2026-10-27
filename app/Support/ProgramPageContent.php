<?php

namespace App\Support;

class ProgramPageContent
{
    /**
     * @return list<string>
     */
    public static function slugs(): array
    {
        return array_keys(self::defaultsBySlug());
    }

    public static function supports(string $slug): bool
    {
        return isset(self::defaultsBySlug()[$slug]);
    }

    public static function layout(string $slug): string
    {
        return match ($slug) {
            'virtual-training', 'non-virtual-training' => 'listing',
            'personal-training' => 'personal',
            'monthly-non-virtual-training', '12-weeks-non-virtual-training', 'half-yearly-non-virtual-training' => 'showcase',
            default => 'hero',
        };
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(string $slug): array
    {
        return self::defaultsBySlug()[$slug] ?? [];
    }

    /**
     * @param  array<string, mixed>|null  $saved
     * @return array<string, mixed>
     */
    public static function merge(string $slug, ?array $saved): array
    {
        return array_replace_recursive(self::defaults($slug), $saved ?? []);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private static function defaultsBySlug(): array
    {
        return [
            'virtual-training' => [
                'kicker' => 'FIND YOUR PLAN',
                'heading' => 'Choose the plan that',
                'heading_accent' => 'fits your journey.',
                'body' => "Whether you're building consistency, working toward a major transformation, or committing to long-term fitness, TFI has a virtual training plan designed around your lifestyle.",
                'cards' => [
                    [
                        'badge' => 'FLEXIBLE',
                        'kicker' => 'START YOUR JOURNEY',
                        'title' => 'Monthly Virtual Training',
                        'body' => 'A flexible starting point for building consistency, improving your daily routine and getting expert guidance without a long-term commitment.',
                        'cta' => 'Start Monthly Plan',
                        'features' => ['Personalised workout plan', 'Coach guidance & support', 'Progress tracking', 'Flexible training schedule'],
                    ],
                    [
                        'badge' => 'TRANSFORM',
                        'kicker' => 'BUILD & TRANSFORM',
                        'title' => '12 Weeks Virtual Transformation',
                        'body' => 'Twelve weeks of structured coaching focused on progressive improvement, sustainable habits, strength and visible transformation.',
                        'cta' => 'Start Transformation',
                        'features' => ['Fully personalised training', 'Regular coach guidance', 'Nutrition & lifestyle support', 'Progress & transformation tracking'],
                    ],
                    [
                        'badge' => 'LONG TERM',
                        'kicker' => 'COMMIT TO YOURSELF',
                        'title' => 'Half-Yearly Virtual Transformation',
                        'body' => 'Six months of structured coaching focused on consistency, progression, sustainable habits, strength and long-term transformation.',
                        'cta' => 'Commit To Your Journey',
                        'features' => ['Complete personalised training', 'Long-term progress planning', 'Nutrition & lifestyle guidance', 'Ongoing coach accountability'],
                    ],
                ],
            ],
            'non-virtual-training' => [
                'kicker' => 'FIND YOUR PLAN',
                'heading' => 'Train in person.',
                'heading_accent' => 'Transform for real.',
                'body' => 'Get hands-on coaching, personalised workouts and real-time guidance in a focused training environment built around your goals, fitness level and lifestyle.',
                'cards' => [
                    [
                        'badge' => 'FLEXIBLE',
                        'kicker' => 'START STRONG',
                        'title' => 'Monthly Personal Training',
                        'body' => 'Perfect for getting started with expert guidance, structured workouts and hands-on coaching that keeps every session focused on your goals.',
                        'cta' => 'Start Monthly Plan',
                        'features' => ['Personalised workout sessions', 'In-person coach guidance', 'Exercise technique correction', 'Progress tracking'],
                    ],
                    [
                        'badge' => 'TRANSFORM',
                        'kicker' => 'BUILD & TRANSFORM',
                        'title' => '12 Weeks Personal Training Transformation',
                        'body' => 'A structured twelve-week transformation journey with progressive workouts, expert coaching and consistent accountability to help you build lasting results.',
                        'cta' => 'Start Transformation',
                        'features' => ['Fully personalised training', 'Hands-on coach support', 'Nutrition & lifestyle guidance', 'Regular progress assessment'],
                    ],
                    [
                        'badge' => 'LONG TERM',
                        'kicker' => 'COMMIT TO YOURSELF',
                        'title' => 'Half-Yearly Personal Training',
                        'body' => 'A complete six-month personalised in-person training journey designed to build strength, improve fitness and make healthy habits part of your lifestyle.',
                        'cta' => 'Commit To Your Journey',
                        'features' => ['Complete personalised training', 'Long-term fitness planning', 'Nutrition & lifestyle support', 'Ongoing coach accountability'],
                    ],
                ],
            ],
            'personal-training' => [
                'intro' => [
                    'kicker' => 'TFI PERSONAL TRAINING',
                    'heading' => 'Training built around',
                    'heading_accent' => 'your needs.',
                    'body' => 'One-on-one coaching designed around your body, your goals, your lifestyle and the areas where you need the most support.',
                ],
                'cards' => [
                    ['label' => 'METABOLIC HEALTH', 'title' => 'Diabetes Management / Blood Sugar Control', 'body' => 'Structured fitness support focused on movement, consistency and healthy lifestyle habits.'],
                    ['label' => "WOMEN'S WELLNESS", 'title' => 'PCOS / PCOD Balanced Nutrition', 'body' => 'Personalised fitness and nutrition guidance designed around individual lifestyle needs.'],
                    ['label' => 'HEART HEALTH', 'title' => 'Hypertension Heart-Healthy Workouts', 'body' => 'Individualised training focused on safe and sustainable fitness routines.'],
                    ['label' => 'MATERNITY FITNESS', 'title' => 'Pre & Post Natal Fitness', 'body' => 'Personalised movement and fitness guidance adapted to individual needs.'],
                    ['label' => 'POSTURE & MOBILITY', 'title' => 'Lower Back & Spine Pain / Posture Correction', 'body' => 'Targeted movement and training support focused on posture, mobility and functional strength.'],
                    ['label' => 'CARDIO FITNESS', 'title' => 'Cardiovascular Health / Cardio Conditioning', 'body' => 'Structured cardio and fitness training tailored to your current fitness level and goals.'],
                    ['label' => 'METABOLIC SUPPORT', 'title' => 'Thyroid Management / Metabolic Support', 'body' => 'Personalised training and lifestyle support focused on sustainable fitness habits.'],
                    ['label' => 'LIFESTYLE SUPPORT', 'title' => 'GERD Fitness Guidance', 'body' => 'Individualised fitness guidance adapted to lifestyle considerations and training needs.'],
                ],
            ],
            'monthly-virtual-training' => [
                'hero' => [
                    'title' => 'Monthly Virtual Training',
                    'title_accent' => 'Train anywhere',
                    'body' => 'Live coaching, personalised workouts and consistent accountability, delivered online. Pick your sessions, training type, trainer level and add-ons below.',
                    'bullets' => [
                        'Live, coach-led sessions from wherever you are',
                        'Training plan personalised to your goals',
                        'Optional nutrition plan and couple training',
                        'Progress tracking to keep you consistent',
                    ],
                    'image' => '',
                    'image_alt' => 'Monthly virtual training',
                ],
            ],
            '12-weeks-virtual-training' => [
                'hero' => [
                    'title' => '12 Weeks Virtual Transformation',
                    'title_accent' => 'A stronger you',
                    'body' => 'A focused 12-week virtual transformation programme with live coaching, evolving workouts and consistent accountability. Build your plan below and see your price update instantly.',
                    'bullets' => [
                        'Live, coach-led sessions 3 to 5 times a week',
                        'A plan that evolves through every phase',
                        'Weekly progress tracking and adjustments',
                        'Optional nutrition plan and couple training',
                    ],
                    'image' => '',
                    'image_alt' => '12 weeks virtual transformation',
                ],
                'journey' => [
                    'kicker' => 'What to expect',
                    'heading' => 'Your 12-week',
                    'heading_accent' => 'journey',
                    'steps' => [
                        [
                            'number' => '01',
                            'period' => 'WEEKS 1–4',
                            'title' => 'Foundation',
                            'body' => 'Assessment, movement technique and routine. We establish your baseline and build consistency from the first week.',
                        ],
                        [
                            'number' => '02',
                            'period' => 'WEEKS 5–8',
                            'title' => 'Build',
                            'body' => 'Progressive training, improved conditioning and stronger movement patterns. Your body starts adapting to the work.',
                        ],
                        [
                            'number' => '03',
                            'period' => 'WEEKS 9–12',
                            'title' => 'Transform',
                            'body' => 'Push performance, consolidate your progress and finish with stronger habits and a clear path for your next phase.',
                        ],
                    ],
                ],
            ],
            'half-yearly-virtual-training' => [
                'hero' => [
                    'title' => 'Half Yearly Virtual Transformation',
                    'title_accent' => 'A lasting change',
                    'body' => 'Our deepest virtual programme: 24 weeks of live coaching, evolving programming and long-term accountability. Build yours below and watch the price update.',
                    'bullets' => [
                        'Live, coach-led sessions 3 to 5 times a week for 24 weeks',
                        'A programme that evolves as you do',
                        'Monthly deep progress reviews and resets',
                        'Optional nutrition plan and couple training',
                    ],
                    'image' => 'images/half-yearly-training.png',
                    'image_alt' => 'Half yearly virtual transformation training',
                ],
                'journey' => [
                    'kicker' => 'What to expect',
                    'heading' => 'Your six-month',
                    'heading_accent' => 'journey',
                    'steps' => [
                        [
                            'number' => '01',
                            'period' => 'MONTHS 1–2',
                            'title' => 'Foundation',
                            'body' => 'Assessment, technique and routine. We build the base that everything else sits on.',
                        ],
                        [
                            'number' => '02',
                            'period' => 'MONTHS 3–4',
                            'title' => 'Build',
                            'body' => 'Progressive overload, new skills and higher volume. Real strength and stamina gains.',
                        ],
                        [
                            'number' => '03',
                            'period' => 'MONTHS 5–6',
                            'title' => 'Transform',
                            'body' => 'Peak and consolidate. You finish with results, habits and a plan to keep going.',
                        ],
                    ],
                ],
            ],
            'monthly-non-virtual-training' => [
                'hero' => [
                    'kicker' => 'TFI Studio · Monthly · In person',
                    'title' => 'Monthly Non-Virtual',
                    'title_accent' => 'Training',
                    'lead' => 'How much can you change your body in a month? More than you think – but only if you have three things: a good exercise plan, smart eating rules, and the right attitude to follow both with focus and determination.',
                    'body' => 'A combination of nutrition & workout, this plan gives you an added advantage of weekly check-ins with the nutritionist, and you can book a call with your coach as well.',
                    'price_label' => 'Monthly plan',
                    'price_per' => 'for one month · workout and nutrition included',
                ],
                'inside' => self::planGallery(),
                'formula' => self::nvFormula(),
            ],
            '12-weeks-non-virtual-training' => [
                'hero' => [
                    'kicker' => 'TFI Studio · 12 Weeks · In person',
                    'title' => '12 Weeks Non-Virtual',
                    'title_accent' => 'Transformation',
                    'lead' => 'How much can you change your body in 12 weeks? More than you think – but only if you have three things: a good exercise plan, smart eating rules, and the right attitude to follow both with focus and determination.',
                    'body' => 'A combination of nutrition & workout, this plan gives you an added advantage of weekly check-ins with the nutritionist, and you can book a call with your coach as well.',
                    'price_label' => '12-week plan',
                    'price_per' => 'for the full 12 weeks · workout and nutrition included',
                ],
                'inside' => self::planGallery(),
                'formula' => self::nvFormula(),
                'journey' => [
                    'kicker' => 'What to expect',
                    'heading' => 'Your 12-week',
                    'heading_accent' => 'journey',
                    'steps' => [
                        [
                            'number' => '01',
                            'period' => 'WEEKS 1–4',
                            'title' => 'Foundation',
                            'body' => 'Assessment, movement quality and habit-building. We set your baseline and lock in a routine you can keep.',
                        ],
                        [
                            'number' => '02',
                            'period' => 'WEEKS 5–8',
                            'title' => 'Build',
                            'body' => 'Training load and intensity step up. Strength, stamina and consistency start to show.',
                        ],
                        [
                            'number' => '03',
                            'period' => 'WEEKS 9–12',
                            'title' => 'Transform',
                            'body' => 'Peak phase. We push, refine and measure your results against week one.',
                        ],
                    ],
                ],
            ],
            'half-yearly-non-virtual-training' => [
                'hero' => [
                    'kicker' => 'TFI Studio · 6 Months · In person',
                    'title' => 'Half Yearly Non-Virtual',
                    'title_accent' => 'Transformation',
                    'lead' => 'How much can you change your body in 6 months? More than you think – but only if you have three things: a good exercise plan, smart eating rules, and the right attitude to follow both with focus and determination.',
                    'body' => 'A combination of nutrition & workout, this plan gives you an added advantage of weekly check-ins with the nutritionist, and you can book a call with your coach as well.',
                    'price_label' => '6-month plan',
                    'price_per' => 'for the full 6 months · workout and nutrition included',
                ],
                'inside' => self::planGallery(),
                'formula' => self::nvFormula(),
                'journey' => [
                    'kicker' => 'What to expect',
                    'heading' => 'Your six-month',
                    'heading_accent' => 'journey',
                    'steps' => [
                        [
                            'number' => '01',
                            'period' => 'MONTHS 1–2',
                            'title' => 'Foundation',
                            'body' => 'Assessment, technique and routine. We build the base that everything else sits on.',
                        ],
                        [
                            'number' => '02',
                            'period' => 'MONTHS 3–4',
                            'title' => 'Build',
                            'body' => 'Progressive overload, new skills and higher volume. Real strength and stamina gains.',
                        ],
                        [
                            'number' => '03',
                            'period' => 'MONTHS 5–6',
                            'title' => 'Transform',
                            'body' => 'Peak and consolidate. You finish with results, habits and a plan to keep going.',
                        ],
                    ],
                ],
            ],
            'diabetes-management' => [
                'hero' => [
                    'title' => 'Diabetes Management',
                    'title_accent' => 'Blood Sugar Control',
                    'body' => "Structured strength, walking and nutrition support for people managing type 2 diabetes or prediabetes, built to work alongside your doctor's advice.",
                    'bullets' => [
                        'Strength, walking and movement plans built around blood sugar control',
                        'Meal-timing and balanced-plate guidance you can actually follow',
                        'Doctor-aligned approach — we never change your medication',
                        'Weekly habit tracking: steps, sleep, water and stress',
                    ],
                    'image' => '',
                    'image_alt' => 'Diabetes management training',
                ],
            ] + self::specializedBody(
                [
                    ['number' => '01', 'period' => 'WEEK 1', 'title' => 'Assess', 'body' => 'We review your health history, medication timing, current activity and goals before a single rep.'],
                    ['number' => '02', 'period' => 'WEEKS 2–6', 'title' => 'Build', 'body' => 'Progressive strength and movement routines, with nutrition adjustments you can actually keep.'],
                    ['number' => '03', 'period' => 'ONGOING', 'title' => 'Sustain', 'body' => 'Regular reviews, plan updates and habits that last long after the programme ends.'],
                ],
                'Built for people who want to take charge.',
                'If you have been advised to improve your lifestyle, this is a safe, structured place to start.',
                [
                    'Living with type 2 diabetes and advised to exercise regularly',
                    'Prediabetic, or told to watch blood sugar levels',
                    'Sedentary routine or a long-hours desk job',
                    'Family history and wanting to act early',
                ],
                [
                    ['title' => 'Strength training', 'body' => 'Building muscle gives your body more capacity to use glucose. We progress load gradually and safely.'],
                    ['title' => 'Daily movement', 'body' => 'Walking targets, short post-meal movement and low-impact cardio that fit real life.'],
                    ['title' => 'Balanced nutrition', 'body' => 'Plate structure, carbohydrate quality and meal timing, kept simple and sustainable.'],
                    ['title' => 'Habit tracking', 'body' => 'Steps, sleep, water and stress, reviewed regularly so small wins add up.'],
                ],
                [
                    'We ask about medication and symptoms, and plan session timing around them',
                    'We never advise changing or stopping your medication',
                    'We watch for signs of low or high blood sugar and adjust the session',
                    'We encourage you to share progress with your doctor or dietitian',
                ]
            ),
            'pcos-pcod-nutrition' => [
                'hero' => [
                    'title' => 'PCOS',
                    'title_accent' => 'PCOD Balanced Nutrition',
                    'body' => 'Strength training, balanced nutrition and routine support for women managing PCOS or PCOD, paced to work with your body, not against it.',
                    'bullets' => [
                        'Strength-led training paced to your cycle and energy',
                        'Balanced nutrition — no crash diets, no extreme restriction',
                        'Sleep and stress routines built into your week',
                        'Regular reviews to keep progress steady and sustainable',
                    ],
                    'image' => '',
                    'image_alt' => 'PCOS PCOD nutrition training',
                ],
            ] + self::specializedBody(
                [
                    ['number' => '01', 'period' => 'WEEK 1', 'title' => 'Assess', 'body' => 'Your history, symptoms, routine and goals, so the plan starts from you.'],
                    ['number' => '02', 'period' => 'WEEKS 2–8', 'title' => 'Build', 'body' => 'Strength-led training with a food plan that fits your culture and kitchen.'],
                    ['number' => '03', 'period' => 'ONGOING', 'title' => 'Sustain', 'body' => 'Review, refine and lock in habits you can keep for years.'],
                ],
                'For women who are tired of extreme plans.',
                'Sustainable habits work better than crash diets, and we build around your cycle and lifestyle.',
                [
                    'Diagnosed with PCOS or PCOD and wanting a structured routine',
                    'Irregular routines, energy dips or cycle-related fatigue',
                    'Struggling with weight or cravings despite trying diets',
                    'Wanting to feel stronger, calmer and more in control',
                ],
                [
                    ['title' => 'Strength training', 'body' => 'Resistance work is a cornerstone for body composition and metabolic health. We build it up steadily.'],
                    ['title' => 'Balanced nutrition', 'body' => 'Protein, fibre and carbohydrate quality, with real-food plans instead of extreme restriction.'],
                    ['title' => 'Stress & recovery', 'body' => 'Realistic training load, rest days and stress habits that support hormonal balance.'],
                    ['title' => 'Cycle-aware tracking', 'body' => 'We adjust intensity around energy levels and track what actually works for you.'],
                ],
                [
                    'We train around your energy levels and avoid pushing through exhaustion',
                    'We never recommend extreme calorie restriction',
                    'We encourage regular reviews with your gynaecologist or endocrinologist',
                    'We do not give medical treatment or adjust medication',
                ]
            ),
            'hypertension-heart-healthy' => [
                'hero' => [
                    'title' => 'Hypertension Heart-Healthy Workouts',
                    'title_accent' => '',
                    'body' => 'Controlled, heart-healthy training for people managing high blood pressure: smart intensity, steady breathing and clear boundaries.',
                    'bullets' => [
                        'Gradual, low-risk progression for heart-healthy training',
                        'Breathing and intensity control to avoid unsafe strain',
                        'Lifestyle guidance: salt awareness, sleep and stress',
                        "Works alongside your doctor's advice and medication",
                    ],
                    'image' => '',
                    'image_alt' => 'Hypertension heart-healthy training',
                ],
            ] + self::specializedBody(
                [
                    ['number' => '01', 'period' => 'WEEK 1', 'title' => 'Assess', 'body' => 'Health history, medication, resting readings you share and your goals.'],
                    ['number' => '02', 'period' => 'WEEKS 2–6', 'title' => 'Build', 'body' => 'Gradual cardio and strength progressions, adjusted to how you feel each session.'],
                    ['number' => '03', 'period' => 'ONGOING', 'title' => 'Sustain', 'body' => 'Regular reviews and a routine you can keep for the long term.'],
                ],
                'Strong and safe, one session at a time.',
                'Exercise is a valuable part of lifestyle care when it is planned well.',
                [
                    'Diagnosed with high blood pressure and cleared to exercise',
                    'Taking BP medication and wanting a safe routine',
                    'High-stress job, long hours and little movement',
                    'Family history and wanting to protect your heart',
                ],
                [
                    ['title' => 'Moderate cardio', 'body' => 'Walking, cycling and low-impact work that builds endurance without sudden spikes.'],
                    ['title' => 'Controlled strength', 'body' => 'Lighter loads, steady tempo and proper breathing, with no breath-holding or maximal lifts.'],
                    ['title' => 'Heart-friendly eating', 'body' => 'Practical guidance on salt, whole foods and portion habits.'],
                    ['title' => 'Stress & sleep', 'body' => 'Recovery habits that support your overall cardiovascular health.'],
                ],
                [
                    'We recommend medical clearance before you start',
                    'We avoid breath-holding and sudden high-intensity efforts',
                    'We pause training if you feel dizzy, short of breath or unwell',
                    'We never advise changes to your medication',
                ]
            ),
            'prenatal-postnatal-fitness' => [
                'hero' => [
                    'title' => 'Pre and Post Natal Fitness Safe Workouts',
                    'title_accent' => '',
                    'body' => 'Gentle, safe and effective training for pregnancy and recovery after birth, with posture, breathing and core re-connection at the heart of it.',
                    'bullets' => [
                        'Trimester-aware training for a safe, comfortable pregnancy',
                        'Postnatal recovery: core, pelvic floor and posture',
                        "Gentle progression with your doctor's clearance",
                        'Energy, mood and sleep support through every stage',
                    ],
                    'image' => '',
                    'image_alt' => 'Prenatal and postnatal fitness',
                ],
            ] + self::specializedBody(
                [
                    ['number' => '01', 'period' => 'STEP 1', 'title' => 'Clearance & assess', 'body' => "We start with your doctor's clearance and your stage, history and goals."],
                    ['number' => '02', 'period' => 'STEP 2', 'title' => 'Train safely', 'body' => 'Stage-appropriate sessions with exercises modified or avoided as needed.'],
                    ['number' => '03', 'period' => 'STEP 3', 'title' => 'Rebuild & return', 'body' => 'Gradual progression back to full fitness after delivery, at your pace.'],
                ],
                'Support for you and your baby.',
                'Every pregnancy and every recovery is different, so we plan around you.',
                [
                    'Expecting and cleared by your doctor to stay active',
                    'New mums returning to exercise after delivery',
                    'Dealing with posture strain, back ache or low energy',
                    'Wanting to feel strong, confident and in control',
                ],
                [
                    ['title' => 'Low-impact movement', 'body' => 'Walking, mobility and gentle conditioning matched to your trimester or recovery stage.'],
                    ['title' => 'Breathing & core', 'body' => 'Deep breathing and gentle core work that supports your body through change.'],
                    ['title' => 'Safe strength', 'body' => 'Light, controlled resistance to support posture, carrying and daily demands.'],
                    ['title' => 'Rest & recovery', 'body' => 'Realistic sessions that respect energy, sleep and a new routine.'],
                ],
                [
                    'We require medical clearance before pregnancy or postnatal training',
                    'We adapt or remove exercises that are not suitable for your stage',
                    'We stop immediately if you experience pain, bleeding, dizziness or concern',
                    "Postnatal return is gradual, with no rush to 'bounce back'",
                ]
            ),
            'back-spine-posture' => [
                'hero' => [
                    'title' => 'Lower Back and Spine Pain',
                    'title_accent' => 'Posture Correction',
                    'body' => 'Mobility, core stability and posture-focused training for people dealing with desk-related stiffness and everyday back discomfort.',
                    'bullets' => [
                        'Core strength and mobility to relieve everyday back strain',
                        'Posture correction for desk jobs and long sitting hours',
                        'Safe movement patterns for lifting, bending and walking',
                        'Progressive plan that builds a stronger, pain-aware spine',
                    ],
                    'image' => '',
                    'image_alt' => 'Back and spine posture training',
                ],
            ] + self::specializedBody(
                [
                    ['number' => '01', 'period' => 'WEEK 1', 'title' => 'Assess', 'body' => 'Posture, movement patterns, history and what aggravates your back.'],
                    ['number' => '02', 'period' => 'WEEKS 2–6', 'title' => 'Build', 'body' => 'Mobility, stability and strength that progress as you feel better.'],
                    ['number' => '03', 'period' => 'ONGOING', 'title' => 'Sustain', 'body' => 'Habits and strength that keep you moving well long term.'],
                ],
                'For backs that are tired of sitting.',
                'Most everyday stiffness responds well to better movement and stronger support muscles.',
                [
                    'Long hours at a desk with stiff or aching back',
                    'Rounded shoulders, forward head or poor posture',
                    'Returning to activity after a period of back discomfort',
                    'Cleared by a doctor or physio to exercise',
                ],
                [
                    ['title' => 'Mobility', 'body' => 'Gentle hip, thoracic and hamstring mobility to release stiffness safely.'],
                    ['title' => 'Core stability', 'body' => 'Controlled core work that supports the spine without strain.'],
                    ['title' => 'Glute & posterior chain', 'body' => 'Stronger glutes and back muscles that take pressure off your lower back.'],
                    ['title' => 'Daily habits', 'body' => 'Desk set-up, movement breaks and simple routines for all-day comfort.'],
                ],
                [
                    'We do not diagnose; persistent or severe pain needs a doctor or physiotherapist',
                    'We stop and refer if you have numbness, weakness or pain radiating down a leg',
                    'We avoid movements that aggravate your symptoms',
                    'We progress gradually, with no pushing through pain',
                ]
            ),
            'cardiovascular-conditioning' => [
                'hero' => [
                    'title' => 'Cardiovascular Health',
                    'title_accent' => 'Cardio Conditioning',
                    'body' => 'Progressive, well-paced cardio programming that improves stamina, recovery and everyday energy, for beginners through to the returning athlete.',
                    'bullets' => [
                        'Structured cardio built on heart-rate zones',
                        'Progressive stamina training for daily life and sport',
                        'Strength work that supports heart and lung health',
                        'Recovery, sleep and tracking to keep gains consistent',
                    ],
                    'image' => '',
                    'image_alt' => 'Cardiovascular conditioning',
                ],
            ] + self::specializedBody(
                [
                    ['number' => '01', 'period' => 'WEEK 1', 'title' => 'Assess', 'body' => 'Current fitness, history and goals, with a simple baseline test.'],
                    ['number' => '02', 'period' => 'WEEKS 2–8', 'title' => 'Build', 'body' => 'Gradual increase in duration and intensity, week by week.'],
                    ['number' => '03', 'period' => 'ONGOING', 'title' => 'Sustain', 'body' => 'Retest, refine and set new targets as you improve.'],
                ],
                'From first walk to first 10K.',
                'Cardio fitness is built gradually, and we pace it so you get stronger without burning out.',
                [
                    'Beginners wanting to build stamina safely',
                    'Runners and walkers working towards a goal',
                    'Low energy or easily out of breath on stairs',
                    'Returning to fitness after a long break',
                ],
                [
                    ['title' => 'Effort zones', 'body' => 'Training at the right intensity, using heart rate or perceived effort.'],
                    ['title' => 'Intervals', 'body' => 'Structured intervals introduced only when your base is ready.'],
                    ['title' => 'Supporting strength', 'body' => 'Leg and core strength that makes your cardio efficient and injury-resistant.'],
                    ['title' => 'Smart recovery', 'body' => 'Rest days, sleep and fuelling that let your body adapt.'],
                ],
                [
                    'We recommend medical clearance if you have any known heart condition',
                    'We build volume and intensity gradually to protect your body',
                    'We stop sessions for chest pain, unusual breathlessness or dizziness',
                    'We balance cardio with strength and recovery',
                ]
            ),
            'thyroid-metabolic-support' => [
                'hero' => [
                    'title' => 'Thyroid Management',
                    'title_accent' => 'Metabolic Support',
                    'body' => 'Consistent training, balanced nutrition and recovery habits for people managing thyroid conditions, with realistic expectations and steady progress.',
                    'bullets' => [
                        'Training and nutrition tuned for energy and metabolism',
                        'Strength work to support body composition',
                        'Realistic routines that respect fatigue and recovery',
                        'Works alongside your medical care and lab follow-ups',
                    ],
                    'image' => '',
                    'image_alt' => 'Thyroid metabolic support',
                ],
            ] + self::specializedBody(
                [
                    ['number' => '01', 'period' => 'WEEK 1', 'title' => 'Assess', 'body' => 'History, medication timing you share, symptoms and your goals.'],
                    ['number' => '02', 'period' => 'WEEKS 2–8', 'title' => 'Build', 'body' => 'Gradual strength and activity progression with a simple nutrition plan.'],
                    ['number' => '03', 'period' => 'ONGOING', 'title' => 'Sustain', 'body' => 'Review, adjust and build habits that last.'],
                ],
                'Patient, steady and sustainable.',
                'Progress may look different for everyone, so we focus on routines and results you can measure.',
                [
                    'Diagnosed with a thyroid condition and under medical care',
                    'Feeling tired, sluggish or struggling with energy',
                    'Finding weight management harder than expected',
                    'Wanting a consistent routine and clear guidance',
                ],
                [
                    ['title' => 'Strength training', 'body' => 'Preserving and building muscle supports metabolism and daily energy.'],
                    ['title' => 'Regular movement', 'body' => 'Cardio and daily activity scaled to your energy on the day.'],
                    ['title' => 'Balanced nutrition', 'body' => 'Whole-food meal structure, protein and fibre, without extreme restriction.'],
                    ['title' => 'Sleep & recovery', 'body' => 'Rest and stress habits that support overall wellbeing.'],
                ],
                [
                    'We do not give medical advice or adjust medication or timing',
                    'We scale sessions to your energy and avoid overtraining',
                    'We encourage regular check-ups and blood work with your doctor',
                    'We keep goals realistic and progress measurable',
                ]
            ),
            'gerd-support' => [
                'hero' => [
                    'title' => 'GERD (Gastroesophageal Reflux Disease)',
                    'title_accent' => '',
                    'body' => 'Reflux-aware training and eating guidance, with smart timing, positions and pacing to help you stay active comfortably.',
                    'bullets' => [
                        'Comfortable training positions that avoid reflux triggers',
                        'Meal structure and timing guidance around workouts',
                        'Breathing and posture work to ease abdominal pressure',
                        'Personalised plan because every trigger is different',
                    ],
                    'image' => '',
                    'image_alt' => 'GERD support training',
                ],
            ] + self::specializedBody(
                [
                    ['number' => '01', 'period' => 'WEEK 1', 'title' => 'Assess', 'body' => 'Symptoms, triggers, meal pattern and goals, so we know what to avoid.'],
                    ['number' => '02', 'period' => 'WEEKS 2–6', 'title' => 'Build', 'body' => 'Comfortable, progressive training with meal adjustments that suit you.'],
                    ['number' => '03', 'period' => 'ONGOING', 'title' => 'Sustain', 'body' => 'Refine your routine as symptoms improve and fitness grows.'],
                ],
                'Stay active without the discomfort.',
                'Small changes to timing, positions and portions can make training far more comfortable.',
                [
                    'Experiencing heartburn or reflux, especially around exercise',
                    'Unsure how meal size and timing affect your workouts',
                    'Avoiding exercise for fear of discomfort',
                    'Under medical care and wanting a supportive routine',
                ],
                [
                    ['title' => 'Timing', 'body' => 'Planning sessions around meals so you are not training on a full stomach.'],
                    ['title' => 'Position choices', 'body' => 'Low-impact movement that avoids inverted or high-pressure positions that can aggravate reflux.'],
                    ['title' => 'Meal structure', 'body' => 'Smaller, balanced meals and spotting your personal trigger foods.'],
                    ['title' => 'Breathing & posture', 'body' => 'Relaxed breathing and upright posture to reduce abdominal pressure.'],
                ],
                [
                    'We do not diagnose; persistent symptoms need medical attention',
                    'We stop and refer you if you have chest pain, difficulty swallowing or unexplained weight loss',
                    'We never adjust your medication',
                    'We personalise because triggers vary from person to person',
                ]
            ),
        ];
    }

    /**
     * @param  list<array{number: string, period: string, title: string, body: string}>  $journeySteps
     * @param  list<string>  $overviewCards
     * @param  list<array{title: string, body: string}>  $approachCards
     * @param  list<string>  $safetyItems
     * @return array<string, mixed>
     */
    private static function specializedBody(
        array $journeySteps,
        string $overviewHeading,
        string $overviewBody,
        array $overviewCards,
        array $approachCards,
        array $safetyItems
    ): array {
        return [
            'journey' => [
                'kicker' => 'What to expect',
                'heading' => 'Your',
                'heading_accent' => 'journey',
                'steps' => $journeySteps,
            ],
            'overview' => [
                'tab' => 'Overview',
                'kicker' => "Who it's for",
                'heading' => $overviewHeading,
                'body' => $overviewBody,
                'cards' => array_map(fn (string $body): array => ['body' => $body], $overviewCards),
            ],
            'approach' => [
                'tab' => 'Our approach',
                'kicker' => 'Our approach',
                'heading' => 'How we',
                'heading_accent' => 'support you',
                'cards' => $approachCards,
            ],
            'safety' => [
                'tab' => 'Safety',
                'kicker' => 'Our promise',
                'heading' => 'Safety comes first.',
                'items' => $safetyItems,
                'note' => 'TFI programmes are fitness and lifestyle support. They are not medical advice, diagnosis or treatment, and do not replace your doctor. Always consult a qualified healthcare professional before starting a new exercise or nutrition plan.',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function nvFormula(): array
    {
        return [
            'kicker' => 'The formula',
            'heading' => 'Three things.',
            'heading_accent' => 'One plan.',
            'body' => 'Your body changes when all three work together, and this plan puts them in one place.',
            'steps' => [
                [
                    'number' => '01',
                    'title' => 'A good exercise plan',
                    'body' => 'A structured, day-wise schedule with sets, reps and intensity, built around your goal.',
                ],
                [
                    'number' => '02',
                    'title' => 'Smart eating rules',
                    'body' => 'A personalised diet plan, with weekly check-ins with the nutritionist to keep you on track.',
                ],
                [
                    'number' => '03',
                    'title' => 'The right attitude',
                    'body' => 'Focus and determination to follow both. Your coach is beside you for every rep, and you can book a call with your coach as well.',
                ],
            ],
        ];
    }

    /**
     * @return list<array{image: string, alt: string}>
     */
    private static function planGallery(): array
    {
        return [
            ['image' => 'images/plan-diet.jpeg', 'alt' => 'Sample personalised diet plan with meals, macros and hydration'],
            ['image' => 'images/plan-workout.png', 'alt' => 'Sample day-wise workout schedule with warm-up, main workout, sets and reps'],
            ['image' => 'images/plan-exercise.gif', 'alt' => 'Exercise demo'],
        ];
    }
}
