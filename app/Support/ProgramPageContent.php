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
                    'kicker' => 'TFI SPECIALISED PERSONAL TRAINING',
                    'heading' => 'Training built around',
                    'heading_accent' => 'your needs.',
                    'body' => 'One-on-one coaching is designed around your body, your goals, your lifestyle and the areas where you need the most support.',
                ],
                'cards' => [
                    ['label' => 'METABOLIC HEALTH', 'title' => 'Type 2 Diabetes & Prediabetes'],
                    ['label' => 'WOMEN’S HORMONAL HEALTH', 'title' => 'PMOS / PCOS / PCOD'],
                    ['label' => 'CARDIOVASCULAR HEALTH', 'title' => 'Hypertension & Hypotension'],
                    ['label' => 'POSTNATAL FITNESS', 'title' => 'Post-Pregnancy Fitness & Recovery'],
                    ['label' => 'SPINAL HEALTH', 'title' => 'Cervical & Lumbar Spine Conditions'],
                    ['label' => 'JOINT HEALTH', 'title' => 'Arthritis & Joint Mobility'],
                    ['label' => 'THYROID HEALTH', 'title' => 'Hypothyroidism & Hyperthyroidism'],
                    ['label' => 'SENIOR FITNESS', 'title' => 'Age-Related Strength, Balance & Mobility'],
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
                    'title' => 'PMOS /',
                    'title_accent' => 'PCOS / PCOD',
                    'body' => "Polyendocrine Metabolic Ovarian Syndrome (PMOS) is the updated name for the condition previously known as Polycystic Ovary Syndrome (PCOS), commonly referred to as PCOD in India.\n\nPMOS is a hormonal and metabolic condition that can affect several aspects of women’s health, including hormone levels, menstrual cycles, ovulation, metabolism and reproductive health. It may also be associated with insulin resistance, weight changes, acne and excessive facial or body hair. Symptoms vary from woman to woman, and not everyone experiences the same combination of symptoms.",
                    'bullets' => [
                        'Progressive resistance exercises to build muscle strength, support insulin sensitivity and improve body composition.',
                        'Structured physical activity to improve cardiovascular fitness, support metabolic health and reduce sedentary time.',
                        'Nutritionist-led guidance on balanced macronutrients and micronutrients, portion control and practical meal planning.',
                        'Daily tracking of physical activity, sleep, hydration, stress and menstrual cycle patterns to support healthy lifestyle habits.',
                    ],
                    'image' => '',
                    'image_alt' => 'PMOS PCOS PCOD training',
                ],
            ] + self::specializedBody(
                [
                    ['number' => '01', 'period' => 'WEEK 1', 'title' => 'Assess', 'body' => 'We understand your health history, current fitness level, lifestyle, individual needs and personal goals before designing your training plan.'],
                    ['number' => '02', 'period' => 'WEEKS 2–8', 'title' => 'Build', 'body' => 'Follow your personalised strength, movement and nutrition plan with progressive training and regular guidance tailored to your needs.'],
                    ['number' => '03', 'period' => 'WEEK 9 ONWARDS', 'title' => 'Sustain', 'body' => 'Review your progress, refine your training plan and build sustainable habits for long-term fitness and wellbeing.'],
                ],
                'Support your hormonal and metabolic health.',
                'Structured exercise and nutrition guidance for women living with PMOS / PCOS / PCOD.',
                [
                    'Diagnosed with PCOS / PCOD and looking for structured exercise support.',
                    'Experiencing irregular menstrual cycles or other hormonal symptoms.',
                    'Managing insulin resistance or changes in body composition.',
                    'Looking to improve fitness, build strength and develop sustainable healthy habits.',
                ],
                [
                    ['title' => 'Strength Training', 'body' => 'Progressive resistance exercises to build muscle strength, support insulin sensitivity and improve body composition.'],
                    ['title' => 'Daily Movement', 'body' => 'Structured cardio and movement routines to improve metabolic health, support cardiovascular fitness and reduce sedentary time.'],
                    ['title' => 'Balanced Nutrition', 'body' => 'Nutritionist-led guidance on balanced meals, protein, fibre, carbohydrates and portion control to support overall metabolic health.'],
                    ['title' => 'Habit Tracking', 'body' => 'Tracking physical activity, sleep, hydration, stress and menstrual cycle patterns to build consistency and monitor progress.'],
                ],
                [
                    'We review your health history, menstrual cycle concerns, current symptoms and fitness level before training.',
                    'We consider your individual needs, energy levels and exercise tolerance when planning training intensity.',
                    'We monitor your response to exercise and adjust training if you experience unusual discomfort, dizziness or excessive fatigue.',
                    'We encourage regular communication with your treating doctor and follow their recommendations throughout your fitness journey.',
                ],
                'Overview',
                'TFI programmes provide fitness and lifestyle support. They are not medical advice, diagnosis or treatment, and do not replace your doctor. Always consult your doctor before starting a new exercise or nutrition plan.'
            ),
            'hypertension-heart-healthy' => [
                'hero' => [
                    'title' => 'Hypertension &',
                    'title_accent' => 'Hypotension',
                    'body' => 'Blood pressure is the force of blood pushing against the walls of the arteries. Hypertension refers to consistently high blood pressure, while hypotension refers to abnormally low blood pressure. Both conditions require individual attention, as they can affect circulation, exercise tolerance and overall wellbeing.',
                    'bullets' => [
                        'Appropriate resistance exercises to build muscle strength while considering individual blood pressure levels and exercise tolerance.',
                        'Suitable aerobic activities and movement routines to support cardiovascular fitness, circulation and everyday mobility.',
                        'Nutritionist-led guidance on balanced macronutrients and micronutrients, portion control and practical meal planning.',
                        'Tracking blood pressure readings as advised by your doctor, physical activity, sleep, hydration and exercise-related symptoms to support safe and consistent habits.',
                    ],
                    'image' => '',
                    'image_alt' => 'Hypertension and hypotension training',
                ],
            ] + self::specializedBody(
                [
                    ['number' => '01', 'period' => 'WEEK 1', 'title' => 'Assess', 'body' => 'We understand your health history, current fitness level, lifestyle, individual needs and personal goals before designing your training plan.'],
                    ['number' => '02', 'period' => 'WEEKS 2–8', 'title' => 'Build', 'body' => 'Follow your personalised strength, movement and nutrition plan with progressive training and regular guidance tailored to your needs.'],
                    ['number' => '03', 'period' => 'WEEK 9 ONWARDS', 'title' => 'Sustain', 'body' => 'Review your progress, refine your training plan and build sustainable habits for long-term fitness and wellbeing.'],
                ],
                'Build fitness with the right guidance.',
                'Structured exercise and nutrition guidance for individuals living with high or low blood pressure.',
                [
                    'Living with hypertension and looking for appropriate exercise support.',
                    'Managing hypotension and seeking guidance suited to individual exercise tolerance.',
                    'Experiencing reduced physical activity and looking to improve overall fitness.',
                    'Wanting to build strength, improve mobility and develop sustainable healthy habits with appropriate medical guidance.',
                ],
                [
                    ['title' => 'Controlled Strength Training', 'body' => 'Gradual resistance exercises with appropriate intensity, breathing techniques and rest periods to support muscle strength and physical fitness.'],
                    ['title' => 'Cardiovascular Fitness', 'body' => 'Individually adapted aerobic activities, warm-ups and cool-downs to improve exercise capacity and support everyday movement.'],
                    ['title' => 'Balanced Nutrition', 'body' => 'Nutritionist-led guidance on balanced macronutrients and micronutrients, portion control and practical meal planning to support overall wellbeing.'],
                    ['title' => 'Blood Pressure & Habit Tracking', 'body' => 'Monitoring blood pressure readings as advised by your doctor, along with physical activity, sleep, hydration and exercise-related symptoms to track progress.'],
                ],
                [
                    'We review your health history, blood pressure condition, medications, symptoms and medical recommendations before training.',
                    'We plan exercise intensity, session duration and rest periods according to your individual fitness level and medical clearance.',
                    'We monitor your response to exercise and stop training if you experience warning signs such as dizziness, fainting, chest discomfort or unusual breathlessness.',
                    'We encourage regular communication with your treating doctor and follow their recommendations throughout your fitness journey.',
                ],
                'Overview',
                'TFI programmes provide fitness and lifestyle support. They are not medical advice, diagnosis or treatment, and do not replace your doctor. Always consult your doctor before starting a new exercise or nutrition plan.'
            ),
            'prenatal-postnatal-fitness' => [
                'hero' => [
                    'title' => 'Post-Pregnancy',
                    'title_accent' => 'Fitness & Recovery',
                    'body' => "Postnatal recovery is the period following childbirth, when a woman’s body gradually adapts and recovers from the physical changes of pregnancy and delivery. Recovery varies depending on individual health, delivery type and any complications.\n\nOur programme provides personalised exercise and nutrition guidance to help mothers gradually rebuild strength, improve fitness and develop healthy habits while respecting their recovery and medical advice.",
                    'bullets' => [
                        'Carefully progressed resistance exercises to rebuild muscle strength and support everyday physical function.',
                        'Appropriate exercises to support core strength, posture, mobility and everyday movement, considering individual recovery needs.',
                        'Nutritionist-led guidance on balanced macronutrients and micronutrients, portion control and practical meal planning, considering individual nutritional needs.',
                        'Monitoring physical activity, sleep, hydration, energy levels and recovery to support consistent and healthy lifestyle habits.',
                    ],
                    'image' => '',
                    'image_alt' => 'Post-pregnancy fitness and recovery',
                ],
            ] + self::specializedBody(
                [
                    ['number' => '01', 'period' => 'WEEK 1', 'title' => 'Assess', 'body' => 'We understand your delivery history, recovery progress, health background, current fitness level and personal goals. We also consider your doctor’s recommendations before planning your exercise programme.'],
                    ['number' => '02', 'period' => 'WEEKS 2–8', 'title' => 'Build', 'body' => 'Follow a personalised exercise and nutrition plan with gradual training progression, adapted to your recovery, energy levels and individual needs.'],
                    ['number' => '03', 'period' => 'WEEK 9 ONWARDS', 'title' => 'Sustain', 'body' => 'Review your progress, adjust your training plan and develop sustainable fitness habits that support your long-term strength, mobility and wellbeing.'],
                ],
                'Rebuild your strength. Return to fitness with confidence.',
                'Structured exercise and nutrition guidance for mothers looking to rebuild their fitness after childbirth, with individual recovery needs and medical advice always considered.',
                [
                    'Recently given birth and looking for appropriate exercise guidance.',
                    'Returning to physical activity after a caesarean or vaginal delivery, with medical clearance.',
                    'Looking to gradually rebuild strength, core stability and everyday mobility.',
                    'Wanting to improve overall fitness and develop sustainable healthy habits while balancing motherhood and recovery.',
                ],
                [
                    ['title' => 'Gradual Strength Training', 'body' => 'Carefully progressed resistance exercises to rebuild overall muscle strength and support everyday physical activities, based on individual recovery and medical clearance.'],
                    ['title' => 'Core & Functional Movement', 'body' => 'Appropriate movement routines to support posture, mobility, core strength and everyday movement without placing unnecessary strain on the recovering body.'],
                    ['title' => 'Balanced Nutrition', 'body' => 'Nutritionist-led guidance on balanced meals, protein, carbohydrates, healthy fats, essential nutrients and portion control, considering individual nutritional needs and breastfeeding where applicable.'],
                    ['title' => 'Recovery & Habit Tracking', 'body' => 'Tracking physical activity, sleep, hydration, energy levels and exercise tolerance to monitor progress and build sustainable healthy habits.'],
                ],
                [
                    'We review your delivery history, recovery progress, health background and current symptoms before planning your training.',
                    'We recommend starting exercise only after appropriate medical clearance and follow the treating doctor’s recommendations.',
                    'We adapt exercise intensity, movement selection and progression to your individual recovery, fitness level and energy levels.',
                    'We stop or modify training if you experience unusual pain, dizziness, increased bleeding or other concerning symptoms, and recommend medical assessment when necessary.',
                    'We refer pelvic-floor concerns, postnatal complications and rehabilitation needs to appropriately qualified healthcare professionals.',
                ],
                'Overview',
                'TFI programmes provide fitness and lifestyle support. They are not medical advice, diagnosis or treatment, and do not replace your doctor or postnatal healthcare team. Always consult your doctor before starting a new exercise or nutrition plan.'
            ),
            'back-spine-posture' => [
                'hero' => [
                    'title' => 'Cervical & Lumbar',
                    'title_accent' => 'Spine Conditions',
                    'body' => "The spine supports the body, protects the spinal cord and enables movement. The cervical spine refers to the neck region, while the lumbar spine refers to the lower-back region. Conditions affecting these areas may cause pain, stiffness, restricted movement and, in some cases, nerve-related symptoms.\n\nOur spinal health programme provides personalised exercise and nutrition guidance to help individuals build overall strength, improve movement and maintain physical fitness while respecting their diagnosed condition, symptoms and medical recommendations.",
                    'bullets' => [
                        'Individually adapted resistance exercises to build supporting muscle strength and improve overall physical function.',
                        'Appropriate movement routines to maintain flexibility, improve mobility and support comfortable everyday activities.',
                        'Nutritionist-led guidance on balanced macronutrients and micronutrients, portion control and practical meal planning to support overall health.',
                        'Monitoring daily activity, movement habits, posture awareness, sleep and exercise-related symptoms to support consistent and healthy routines.',
                    ],
                    'image' => '',
                    'image_alt' => 'Cervical and lumbar spine conditions training',
                ],
            ] + self::specializedBody(
                [
                    ['number' => '01', 'period' => 'WEEK 1', 'title' => 'Assess', 'body' => 'We understand your spinal condition, medical history, current symptoms, movement limitations, fitness level and medical recommendations before designing your personalised training plan.'],
                    ['number' => '02', 'period' => 'WEEKS 2–8', 'title' => 'Build', 'body' => 'Follow your personalised strength, mobility and movement plan with gradual exercise progression, adapted to your cervical or lumbar condition, individual needs and physical capacity.'],
                    ['number' => '03', 'period' => 'WEEK 9 ONWARDS', 'title' => 'Sustain', 'body' => 'Review your progress, refine your training plan and develop sustainable exercise habits to maintain strength, mobility and everyday physical function.'],
                ],
                'Build strength. Improve movement. Support your spinal health.',
                'Structured exercise and nutrition guidance for individuals experiencing cervical or lumbar spine conditions, with training adapted to their symptoms, physical capacity and medical recommendations.',
                [
                    'Living with cervical spine conditions and looking for appropriate fitness guidance.',
                    'Experiencing lower-back discomfort or diagnosed lumbar spine conditions.',
                    'Facing movement limitations, stiffness or reduced physical activity due to spinal concerns.',
                    'Looking to build overall strength, improve mobility and develop sustainable healthy habits with appropriate medical guidance.',
                ],
                [
                    ['title' => 'Controlled Strength Training', 'body' => 'Individually adapted resistance exercises to build muscle strength, support spinal stability and improve everyday physical function within comfortable movement ranges.'],
                    ['title' => 'Mobility & Movement Training', 'body' => 'Carefully selected movement routines to maintain neck and lower-back mobility, improve flexibility and support comfortable everyday activities without aggravating symptoms.'],
                    ['title' => 'Balanced Nutrition', 'body' => 'Nutritionist-led guidance on balanced macronutrients and micronutrients, portion control and practical meal planning to support overall health and wellbeing.'],
                    ['title' => 'Posture & Habit Tracking', 'body' => 'Tracking daily activity, movement habits, posture awareness, sleep and exercise-related symptoms to monitor progress and develop sustainable healthy routines.'],
                ],
                [
                    'We review your spinal condition, medical history, current symptoms, movement limitations and medical recommendations before training.',
                    'We select appropriate exercises and plan training intensity according to your individual condition, physical capacity and medical clearance.',
                    'We monitor your response to exercise and modify or stop training if you experience increasing pain, numbness, tingling or unusual discomfort.',
                    'We encourage regular communication with your treating doctor and follow their recommendations throughout your fitness journey.',
                    'We refer neurological symptoms, worsening spinal concerns and rehabilitation needs to appropriately qualified healthcare professionals.',
                ],
                'Overview',
                'TFI programmes provide fitness and lifestyle support. They are not medical advice, diagnosis, spinal rehabilitation or treatment, and do not replace your doctor or physiotherapist. Always consult your doctor before starting a new exercise or nutrition plan.'
            ),
            'arthritis-joint-mobility' => [
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
                    'title' => 'Hypothyroidism &',
                    'title_accent' => 'Hyperthyroidism',
                    'body' => "The thyroid is a small gland in the neck that produces hormones responsible for regulating metabolism, energy levels and several important body functions.\n\nHypothyroidism occurs when the thyroid gland produces too little thyroid hormone, which can slow down body functions.\n\nHyperthyroidism occurs when the thyroid gland produces too much thyroid hormone, which can speed up body functions.",
                    'bullets' => [
                        'Progressive resistance exercises to build muscle strength, support muscle function and maintain healthy body composition.',
                        'Individualised exercise routines adapted to your energy levels, symptoms and fitness capacity.',
                        'Nutritionist-led guidance on balanced macronutrients and micronutrients, portion control and practical meal planning.',
                        'Monitoring physical activity, sleep, hydration, energy levels and recovery to support consistent healthy habits.',
                    ],
                    'image' => '',
                    'image_alt' => 'Hypothyroidism and hyperthyroidism training',
                ],
            ] + self::specializedBody(
                [
                    ['number' => '01', 'period' => 'WEEK 1', 'title' => 'Assess', 'body' => 'We understand your health history, current fitness level, lifestyle, individual needs and personal goals before designing your training plan.'],
                    ['number' => '02', 'period' => 'WEEKS 2–8', 'title' => 'Build', 'body' => 'Follow your personalised strength, movement and nutrition plan with progressive training and regular guidance tailored to your needs.'],
                    ['number' => '03', 'period' => 'WEEK 9 ONWARDS', 'title' => 'Sustain', 'body' => 'Review your progress, refine your training plan and build sustainable habits for long-term fitness and wellbeing.'],
                ],
                'Build strength. Improve your fitness.',
                'Structured exercise and nutrition guidance for people living with hypothyroidism or hyperthyroidism.',
                [
                    'Living with hypothyroidism and looking for structured exercise support.',
                    'Diagnosed with hyperthyroidism and seeking appropriate fitness guidance.',
                    'Experiencing changes in energy levels, strength or physical fitness.',
                    'Looking to improve body composition and build sustainable healthy habits.',
                ],
                [
                    ['title' => 'Strength Training', 'body' => 'Progressive resistance exercises to build muscle strength, maintain healthy body composition and improve physical fitness.'],
                    ['title' => 'Daily Movement', 'body' => 'Structured movement routines adapted to your energy levels and fitness capacity to improve mobility and reduce sedentary time.'],
                    ['title' => 'Balanced Nutrition', 'body' => 'Nutritionist-led guidance on balanced meals, essential nutrients, protein intake and portion control to support overall health.'],
                    ['title' => 'Habit Tracking', 'body' => 'Tracking daily activity, sleep, hydration and energy levels to build consistency and monitor progress.'],
                ],
                [
                    'We review your health history, current symptoms, fitness level and any exercise restrictions before training.',
                    'We consider your energy levels and individual needs when planning exercise intensity and session duration.',
                    'We monitor your response to exercise and adjust or stop training if you experience unusual symptoms such as dizziness, excessive fatigue or palpitations.',
                    'We encourage regular communication with your treating doctor and follow their recommendations throughout your fitness journey.',
                ],
                'Overview',
                'TFI programmes provide fitness and lifestyle support. They are not medical advice, diagnosis or treatment, and do not replace your doctor. Always consult your doctor before starting a new exercise or nutrition plan.'
            ),
            'age-related-strength-balance-mobility' => [
                'hero' => [
                    'title' => 'GERD',
                    'title_accent' => "Gastro\u{00AD}esophageal Reflux Disease",
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
        array $safetyItems,
        string $overviewKicker = "Who it's for",
        string $safetyNote = 'TFI programmes are fitness and lifestyle support. They are not medical advice, diagnosis or treatment, and do not replace your doctor. Always consult a qualified healthcare professional before starting a new exercise or nutrition plan.'
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
                'kicker' => $overviewKicker,
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
                'note' => $safetyNote,
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
