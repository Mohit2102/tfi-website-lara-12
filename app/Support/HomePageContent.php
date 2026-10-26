<?php

namespace App\Support;

use Illuminate\Support\Arr;

class HomePageContent
{
    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'hero' => [
                ['image' => 'images/hero-7.png', 'alt' => 'Personal training'],
                ['image' => 'images/hero-8.png', 'alt' => 'Strength training'],
                ['image' => 'images/hero-9.png', 'alt' => 'Group fitness'],
            ],
            'stats' => [
                ['value' => '2000', 'suffix' => '+', 'label' => 'Members trained'],
                ['value' => '15', 'suffix' => '+', 'label' => 'Certified coaches'],
                ['value' => '3', 'suffix' => '', 'label' => 'Fitness programs'],
                ['value' => '12', 'suffix' => '+', 'label' => 'Years of coaching'],
            ],
            'intro' => [
                'eyebrow' => 'YOUR FITNESS. YOUR GOALS. YOUR JOURNEY.',
                'heading' => 'Fitness that is',
                'heading_accent' => 'built around you.',
                'subtitle' => 'Personalised Fitness Training, Wherever You Are',
                'body' => 'At TIME TO FIT INDIA, we first understand your body, fitness level, lifestyle and goals. Then, we create a personalised training approach designed around you — with expert guidance and support, wherever you are.',
                'steps' => [
                    ['title' => 'Know You', 'text' => 'Your body, habits, health and goals come first.'],
                    ['title' => 'Build It', 'text' => 'A plan that fits your schedule, level and preferences.'],
                    ['title' => 'Own It', 'text' => 'Track every win, grow stronger and stay confident.'],
                ],
            ],
            'journey' => [
                'kicker' => 'YOUR FITNESS JOURNEY STARTS HERE',
                'heading' => "Understand. Assess.\nPlan. Train. Progress.",
                'body' => 'Your fitness journey starts with understanding where you are, where you want to go and what works best for you.',
                'footer' => 'A personalised journey, thoughtfully built around you.',
            ],
            'programs' => [
                'kicker' => 'Find Your Program',
                'heading' => 'Every body. Every goal. One team.',
                'body' => 'Whether you train at home, need a complete fitness plan, or prefer personal coaching, TFI has a program built around you.',
                'cards' => [
                    [
                        'title' => 'Virtual Personal Training',
                        'body' => 'Train from the comfort of home with live 1-on-1 coaching, personalised workouts and flexible schedules designed to fit your lifestyle and fitness goals.',
                        'cta' => 'Explore Virtual Training',
                        'image' => 'images/program-1.jpg',
                    ],
                    [
                        'title' => 'Workout & Nutrition Plans',
                        'body' => 'Follow a structured fitness journey with personalised workout routines and nutrition guidance tailored to your goals, preferences and daily routine.',
                        'cta' => 'Explore Fitness Plans',
                        'image' => 'images/program-2.jpg',
                    ],
                    [
                        'title' => 'Specialized Personal Training',
                        'body' => 'Get focused 1-on-1 coaching for your individual fitness needs, from building strength and improving mobility to working towards specific performance goals.',
                        'cta' => 'Explore Personal Training',
                        'image' => 'images/program-3.jpg',
                    ],
                ],
            ],
            'why' => [
                'kicker' => 'THE TFI EDGE',
                'heading' => 'Why',
                'heading_accent' => 'TIME TO FIT INDIA?',
                'subtitle' => 'A personalized approach to online fitness coaching.',
                'cards' => [
                    ['title' => "YOUR BODY.\nYOUR GOALS.\nYOUR STARTING POINT.", 'body' => 'We assess your fitness level, lifestyle and goals to create a training approach that works for you.', 'image' => 'images/why-coaches.jpg'],
                    ['title' => "YOUR GOALS.\nYOUR PERSONAL\nFITNESS PLAN.", 'body' => 'Your personalized plan aligns your fitness goals, training level and daily schedule for consistent progress.', 'image' => 'images/why-small-batches.jpg'],
                    ['title' => "EXPERT GUIDANCE.\nSMARTER MOVEMENT.\nBETTER TRAINING.", 'body' => 'Get expert coaching, exercise guidance and ongoing support to improve your technique and train with confidence.', 'image' => 'images/why-nutrition.jpg'],
                    ['title' => "YOUR SPACE.\nYOUR SCHEDULE.\nYOUR FITNESS.", 'body' => 'Train from your preferred location with flexible online sessions that fit your routine and schedule.', 'image' => 'images/why-scheduling.jpg'],
                    ['title' => "TRACK YOUR EFFORT.\nMEASURE YOUR RESULTS.\nKEEP PROGRESSING.", 'body' => 'Track your consistency, strength and fitness improvements, then adjust your plan as you progress.', 'image' => 'images/why-results.jpg'],
                ],
                'team_heading' => "Qualified People.\nProfessional Coaching.",
                'team_body' => 'Meet our trainers and explore their certifications, experience, qualifications and coaching expertise.',
                'team_image' => 'images/why-team.png',
            ],
            'founder' => [
                'kicker' => 'MEET THE FOUNDER',
                'name' => 'Santhosh M',
                'image' => 'images/santhosh-land.png',
                'caption' => 'Founder · Head Coach · Personal Trainer',
                'role' => 'ACE-Certified Personal Trainer',
                'experience' => '12+',
                'experience_label' => "Years in\nFitness",
                'lead' => 'I believe the right fitness journey begins with understanding the person — their goals, lifestyle and individual needs.',
                'bio' => "I'm Santhosh M, an ACE-Certified Personal Trainer with 12+ years of experience in the fitness industry.\n\nOver the years, I've worked with people across different fitness levels and goals, including people over 40 and individuals with specific health considerations.",
                'quote' => "Fitness should be personal.\nTraining should have a purpose.",
            ],
            'testimonials' => [
                'kicker' => 'Loved by members',
                'heading' => 'Real bodies. Real transformations.',
                'items' => [
                    ['quote' => 'I lost 12 kilos in five months without starving myself. The coaches actually track your progress — it\'s not just a gym, it\'s a system.', 'name' => 'Member', 'program' => 'Weight Loss Program'],
                    ['quote' => 'Best personal training I\'ve done. My trainer corrects every rep and actually pushes me on the days I don\'t want to show up.', 'name' => 'Member', 'program' => 'Specialized Live Personal Training'],
                    ['quote' => 'The Zumba classes are the highlight of my week — high energy, zero judgement, real results.', 'name' => 'Member', 'program' => 'Zumba & Dance Fitness'],
                    ['quote' => 'TFI\'s nutrition coaching changed how I eat completely. Down eight kilos and still eating food I actually enjoy.', 'name' => 'Member', 'program' => 'Nutrition Coaching'],
                    ['quote' => 'The coaches make every workout feel purposeful. I\'ve become stronger, more confident and more consistent.', 'name' => 'Member', 'program' => 'Strength & Conditioning'],
                    ['quote' => 'I finally found a routine that fits my lifestyle. The team understands that everyone\'s fitness journey is different.', 'name' => 'Member', 'program' => 'Specialized Live Personal Training'],
                    ['quote' => 'Every session has a clear purpose. The support, accountability and attention to technique made a huge difference.', 'name' => 'Member', 'program' => 'Fitness Program'],
                    ['quote' => 'TFI made fitness feel simple and achievable. I enjoy coming to every session and can genuinely see the progress.', 'name' => 'Member', 'program' => 'Fitness & Wellness'],
                ],
            ],
            'who' => [
                'kicker' => 'WHO WE TRAIN',
                'heading' => "Different people.\nDifferent goals. Different needs.",
                'subtitle' => 'One personalised approach.',
                'men' => 'Helping men build strength, improve fitness and stay active through every stage of life.',
                'women' => 'Helping women become stronger, fitter and more confident at every stage of life.',
                'focus_heading' => "Your body.\nYour goals.\nYour starting point.",
                'focus_body' => 'Whether your goal is to lose fat, build muscle, improve stamina, move better or become stronger, we start with your current level and build from there.',
            ],
            'bmi' => [
                'kicker' => 'HEALTH CHECK',
                'heading' => 'Calculate your',
                'heading_accent' => 'BMI',
                'image' => 'images/bmi-fitness.png',
            ],
            'contact' => [
                'heading' => 'Ready to get fit?',
                'body' => 'Tell us your goal — we\'ll match you with the right coach, program and schedule. Free trial session available this week.',
                'flag' => 'Get a free trial session when you enquire this week',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $saved
     * @return array<string, mixed>
     */
    public static function merge(?array $saved): array
    {
        return array_replace_recursive(self::defaults(), $saved ?? []);
    }

    /**
     * @param  array<string, mixed>  $content
     */
    public static function apply(string $html, array $content): string
    {
        foreach (Arr::dot($content) as $key => $value) {
            if (! is_scalar($value)) {
                continue;
            }

            $html = self::replaceMarked($html, $key, (string) $value);
        }

        return $html;
    }

    private static function replaceMarked(string $html, string $key, string $value): string
    {
        $quoted = preg_quote($key, '/');
        $safe = nl2br(e($value), false);
        $attr = e($value);

        $html = preg_replace_callback(
            '/(data-cms="'.$quoted.'"[^>]*>)(.*?)(<\/)/s',
            fn (array $match): string => $match[1].$safe.$match[3],
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/src="[^"]*"([^>]*\s)?data-cms-src="'.$quoted.'"/',
            fn (array $match): string => 'src="'.$attr.'"'.($match[1] ?? '').'data-cms-src="'.$key.'"',
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/data-cms-src="'.$quoted.'"([^>]*)\ssrc="[^"]*"/',
            fn (array $match): string => 'data-cms-src="'.$key.'"'.$match[1].' src="'.$attr.'"',
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/alt="[^"]*"([^>]*\s)?data-cms-alt="'.$quoted.'"/',
            fn (array $match): string => 'alt="'.$attr.'"'.($match[1] ?? '').'data-cms-alt="'.$key.'"',
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/style="background-image:url\(\'[^\']*\'\)"([^>]*\s)?data-cms-bg="'.$quoted.'"/',
            fn (array $match): string => 'style="background-image:url(\''.$attr.'\')"'.($match[1] ?? ' ').'data-cms-bg="'.$key.'"',
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/data-cms-bg="'.$quoted.'"([^>]*)style="background-image:url\(\'[^\']*\'\)"/',
            fn (array $match): string => 'data-cms-bg="'.$key.'"'.$match[1].'style="background-image:url(\''.$attr.'\')"',
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '/data-target="[^"]*"([^>]*\s)?data-cms-target="'.$quoted.'"/',
            fn (array $match): string => 'data-target="'.$attr.'"'.($match[1] ?? '').'data-cms-target="'.$key.'"',
            $html
        ) ?? $html;

        return preg_replace_callback(
            '/data-cms-target="'.$quoted.'"([^>]*)data-target="[^"]*"/',
            fn (array $match): string => 'data-cms-target="'.$key.'"'.$match[1].'data-target="'.$attr.'"',
            $html
        ) ?? $html;
    }
}
