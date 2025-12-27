<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Profile V2 - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&family=Noto+Sans:wght@400;500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#EB1542",
                        "primary2": "#071E62",
                        "title": "#020202",
                        "text": "#525252",
                        "borders": "#E8E8E8",
                        "background-light": "#f5f5f5",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Lexend", "sans-serif"],
                        "body": ["Noto Sans", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "2xl": "1rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
    <link rel="stylesheet" href="{{ asset('assets/css/profile-v2.css') }}">
</head>
<body class="bg-background-light dark:bg-background-dark text-title dark:text-white min-h-screen flex flex-col">
    <main class="flex-1 w-full max-w-7xl mx-auto px-4 lg:px-8 py-6">
        <div class="bg-white dark:bg-[#1a222c] rounded-xl shadow-sm overflow-hidden mb-6 border border-borders">
            <div class="h-48 md:h-64 w-full bg-cover bg-center relative" data-alt="Abstract blue geometric header background" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuB-6qpw6EwK0kqFhacloOhTmijg1vIDLcjVmJWoWCa7ivU5DhA2euz-gLp5PQAo2UYFrtoyS5ZnwTZ61W9w0LH3x3J3KB6jrUxrx77qSTPxq1cWjeBOLifBA4NA-S1F-8WMfHZRROURyFCH9handvMxFRePM3R_4YvlyiKiURL4LyaB56WMnf9Fj3GuVXiE1qANUbzfKe_g-Ej8nYDtkzlz0Uoox1q5Npyc5nrfG9wAGvDBbiVUZ6s7VOqqRY1ik0OkMlRvIfPQulc");'>
                <button class="absolute top-4 right-4 bg-white/20 backdrop-blur-md hover:bg-white/30 text-white p-2 rounded-lg transition">
                    <span class="material-symbols-outlined">edit</span>
                </button>
            </div>
            <div class="px-6 pb-6 relative">
                <div class="flex flex-col md:flex-row items-start md:items-end gap-6 -mt-16 mb-4">
                    <div class="relative shrink-0">
                        <div class="size-32 md:size-40 rounded-full border-4 border-white dark:border-[#1a222c] bg-gray-200 bg-cover bg-center shadow-md" data-alt="Profile photo of Alex Johnson" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuAYxffWduA5caHNEHfsUoOMD0cmY8pqBiukXg49zv_A2rgcsRqechW3gRUkbE4p7btMjKub2mmHKGT8p_hLND5zCVVNkV4J9anOYK-Snn8DE8sS7vWV7R7ZR_8udiFFBuL4BXz94Pfg8_oY4Za4Huiy2yVZ0l0NdE6PWItQIuUmE4Rx9IjkSSFRbGpFosGrjruVEL8x4lTtwlsWq46uT-mOCLY2nPfozbYRVJvSdW7vUIiCL89R6wvrVhf_YwhI4kcOwbSog6NTqBQ");'>
                        </div>
                        <div class="absolute bottom-2 right-2 bg-green-500 border-2 border-white dark:border-[#1a222c] rounded-full p-1 text-white flex items-center justify-center" title="Student Verified">
                            <span class="material-symbols-outlined text-[16px] leading-none">check_circle</span>
                        </div>
                    </div>
                    <div class="flex-1 pt-2 md:pt-0 md:pb-2">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <h1 class="text-2xl md:text-3xl font-bold text-title dark:text-white">Alex Johnson</h1>
                                    <span class="inline-flex items-center gap-1 bg-primary/10 text-primary text-xs font-bold px-2 py-1 rounded-full">
                                        <span class="material-symbols-outlined text-[14px]">verified</span>
                                        Student Verified
                                    </span>
                                </div>
                                <p class="text-title dark:text-gray-200 text-base md:text-lg font-medium leading-normal mb-1">Computer Science Major at State University</p>
                                <div class="flex flex-wrap items-center gap-4 text-text dark:text-gray-400 text-sm">
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">location_on</span> San Francisco, CA</span>
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">school</span> Class of 2025</span>
                                    <a class="text-primary hover:underline font-medium" href="#">Contact info</a>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <button class="flex items-center justify-center gap-2 h-10 px-6 bg-primary hover:opacity-90 text-white text-sm font-bold rounded-lg transition-colors shadow-sm">
                                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                                    Connect
                                </button>
                                <button class="flex items-center justify-center gap-2 h-10 px-6 bg-white dark:bg-[#2a3441] border border-borders dark:border-[#374151] text-title dark:text-white hover:bg-gray-50 dark:hover:bg-[#3a4555] text-sm font-bold rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-[20px]">download</span>
                                    Resume
                                </button>
                                <button class="flex items-center justify-center h-10 w-10 bg-white dark:bg-[#2a3441] border border-borders dark:border-[#374151] text-text dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-[#3a4555] rounded-lg transition-colors">
                                    <span class="material-symbols-outlined">more_horiz</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-8 space-y-6">
                <section class="bg-white dark:bg-[#1a222c] rounded-xl shadow-sm p-6 border border-borders">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-title dark:text-white">About</h3>
                        <button class="text-text hover:text-primary transition-colors">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                    </div>
                    <p class="text-title dark:text-gray-300 text-base font-normal leading-relaxed">
                        Passionate Computer Science student with a strong foundation in software engineering principles. I have hands-on experience in full-stack web development through internships and academic projects. I am particularly interested in AI/ML applications and cloud computing. Currently seeking Summer 2024 internship opportunities to apply my skills in a challenging environment. I am a quick learner, a collaborative team player, and always eager to tackle complex problems.
                    </p>
                </section>
                <section class="bg-white dark:bg-[#1a222c] rounded-xl shadow-sm p-6 border border-borders">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-title dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary">rocket_launch</span>
                            Projects and Portfolio
                        </h3>
                        <div class="flex gap-2">
                            <button class="text-text hover:text-primary transition-colors"><span class="material-symbols-outlined">add</span></button>
                            <button class="text-text hover:text-primary transition-colors"><span class="material-symbols-outlined">edit</span></button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="group border border-borders dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition-shadow dark:hover:bg-[#202936]">
                            <div class="h-40 bg-gray-100 dark:bg-[#2a3441] bg-cover bg-center relative" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDBNNcaHAa5Y2d7erqrek1LZAl7bWI7qEBkeqydEWR_HdY0l87cU2HS_EbJ8gPSBY_HwRDTLLhG3ReFCnMrFCpBBeuCGNwzizF2Ber826Qrc5tKSmzPc28CHcewSfgUodKMu9Tb7MqZDjjgbk-SXdzDCW5gbTTmlde6OYRPO4Dyo9nh6RYYYf6iBv2bT9FCh4t5R1lMzDXppMpbOrBcvgJEWoTLjdGDhTmPszvDvsZtEgG8WxUji6qOuYmR1Dji1LRrxtr8sSPKVa8");'>
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <a class="flex items-center gap-2 bg-white text-gray-900 font-semibold px-4 py-2 rounded-full text-sm hover:bg-gray-100 transition-colors" href="#">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span> View
                                    </a>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-bold text-title dark:text-white text-base leading-tight">E-commerce Dashboard</h4>
                                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-primary2/10 text-primary2 dark:bg-primary2/20 dark:text-primary2">Web App</span>
                                </div>
                                <p class="text-text dark:text-gray-400 text-sm mb-3 line-clamp-2">
                                    A comprehensive dashboard for managing online store inventory and sales analytics. Built with React and Firebase.
                                </p>
                                <div class="flex flex-wrap gap-2 text-xs">
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-[#374151] text-text dark:text-gray-300 rounded">React</span>
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-[#374151] text-text dark:text-gray-300 rounded">Tailwind</span>
                                </div>
                            </div>
                        </div>
                        <div class="group border border-borders dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition-shadow dark:hover:bg-[#202936]">
                            <div class="h-40 bg-gray-100 dark:bg-[#2a3441] flex items-center justify-center relative">
                                <span class="material-symbols-outlined text-gray-400 text-5xl">code</span>
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <a class="flex items-center gap-2 bg-white text-gray-900 font-semibold px-4 py-2 rounded-full text-sm hover:bg-gray-100 transition-colors" href="#">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span> View
                                    </a>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-bold text-title dark:text-white text-base leading-tight">NLP Sentiment Analysis</h4>
                                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-primary/10 text-primary dark:bg-primary/20 dark:text-primary">AI / ML</span>
                                </div>
                                <p class="text-text dark:text-gray-400 text-sm mb-3 line-clamp-2">
                                    Python script utilizing NLTK and Scikit-learn to analyze sentiment in social media comments with 85% accuracy.
                                </p>
                                <div class="flex flex-wrap gap-2 text-xs">
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-[#374151] text-text dark:text-gray-300 rounded">Python</span>
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-[#374151] text-text dark:text-gray-300 rounded">Jupyter</span>
                                </div>
                            </div>
                        </div>
                        <div class="group border border-borders dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition-shadow dark:hover:bg-[#202936]">
                            <div class="h-40 bg-gray-100 dark:bg-[#2a3441] flex items-center justify-center relative bg-gradient-to-br from-indigo-500 to-purple-600">
                                <span class="material-symbols-outlined text-white/80 text-5xl">smartphone</span>
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <a class="flex items-center gap-2 bg-white text-gray-900 font-semibold px-4 py-2 rounded-full text-sm hover:bg-gray-100 transition-colors" href="#">
                                        <span class="material-symbols-outlined text-[18px]">visibility</span> View
                                    </a>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-bold text-title dark:text-white text-base leading-tight">Campus Event Finder</h4>
                                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-primary2/10 text-primary2 dark:bg-primary2/20 dark:text-primary2">Mobile App</span>
                                </div>
                                <p class="text-text dark:text-gray-400 text-sm mb-3 line-clamp-2">
                                    A cross-platform mobile application helping students discover and register for campus events.
                                </p>
                                <div class="flex flex-wrap gap-2 text-xs">
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-[#374151] text-text dark:text-gray-300 rounded">Flutter</span>
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-[#374151] text-text dark:text-gray-300 rounded">Dart</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-borders dark:border-gray-700 text-center">
                        <button class="text-primary text-sm font-semibold hover:underline inline-flex items-center gap-1">
                            View all projects <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </button>
                    </div>
                </section>
                <section class="bg-white dark:bg-[#1a222c] rounded-xl shadow-sm p-6 border border-borders">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-title dark:text-white">Education</h3>
                        <div class="flex gap-2">
                            <button class="text-text hover:text-primary transition-colors"><span class="material-symbols-outlined">add</span></button>
                            <button class="text-text hover:text-primary transition-colors"><span class="material-symbols-outlined">edit</span></button>
                        </div>
                    </div>
                    <div class="flex flex-col gap-6">
                        <div class="flex gap-4">
                            <div class="text-title dark:text-white flex items-center justify-center rounded-lg bg-gray-100 dark:bg-[#2a3441] shrink-0 size-14">
                                <span class="material-symbols-outlined text-[28px]">school</span>
                            </div>
                            <div class="flex flex-1 flex-col justify-center">
                                <div class="flex justify-between items-start">
                                    <h4 class="text-title dark:text-white text-base font-bold leading-normal">State University</h4>
                                </div>
                                <p class="text-title dark:text-gray-200 text-sm font-medium leading-normal">Bachelor of Science in Computer Science</p>
                                <p class="text-text dark:text-gray-400 text-sm font-normal leading-normal mt-1">2021 - 2025 | GPA: 3.8/4.0</p>
                                <p class="text-text dark:text-gray-400 text-sm font-normal leading-normal mt-2">
                                    <span class="font-medium">Activities:</span> Hackathon Club President, Teaching Assistant for CS101, Intramural Soccer.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="bg-white dark:bg-[#1a222c] rounded-xl shadow-sm p-6 border border-borders">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-title dark:text-white">Experience</h3>
                        <div class="flex gap-2">
                            <button class="text-text hover:text-primary transition-colors"><span class="material-symbols-outlined">add</span></button>
                            <button class="text-text hover:text-primary transition-colors"><span class="material-symbols-outlined">edit</span></button>
                        </div>
                    </div>
                    <div class="grid grid-cols-[40px_1fr] gap-x-3">
                        <div class="flex flex-col items-center gap-1 pt-1">
                            <div class="text-primary bg-primary/10 rounded-full p-1.5 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[20px]">work</span>
                            </div>
                            <div class="w-[2px] bg-borders dark:bg-[#374151] h-full grow my-1"></div>
                        </div>
                        <div class="flex flex-1 flex-col pb-6">
                            <h4 class="text-title dark:text-white text-base font-bold leading-normal">Software Engineering Intern</h4>
                            <p class="text-title dark:text-gray-200 text-sm font-medium">Tech Corp · Internship</p>
                            <p class="text-text dark:text-gray-400 text-xs font-normal mb-2">Jun 2023 - Aug 2023 · 3 mos · San Francisco, CA</p>
                            <p class="text-text dark:text-gray-300 text-sm leading-relaxed">
                                Developed a new feature for the internal dashboard using React and Node.js. Optimized API endpoints improving response time by 20%. Collaborated with senior engineers on system architecture design.
                            </p>
                            <div class="flex gap-2 mt-3">
                                <span class="px-2 py-1 bg-gray-100 dark:bg-[#2a3441] text-xs font-medium text-text dark:text-gray-300 rounded">React</span>
                                <span class="px-2 py-1 bg-gray-100 dark:bg-[#2a3441] text-xs font-medium text-text dark:text-gray-300 rounded">Node.js</span>
                            </div>
                        </div>
                        <div class="flex flex-col items-center gap-1 pt-1">
                            <div class="text-text bg-gray-100 dark:bg-[#2a3441] rounded-full p-1.5 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[20px]">science</span>
                            </div>
                            <div class="w-[2px] bg-borders dark:bg-[#374151] h-full grow my-1"></div>
                        </div>
                        <div class="flex flex-1 flex-col pb-6">
                            <h4 class="text-title dark:text-white text-base font-bold leading-normal">Research Assistant</h4>
                            <p class="text-title dark:text-gray-200 text-sm font-medium">State University · Part-time</p>
                            <p class="text-text dark:text-gray-400 text-xs font-normal mb-2">Jan 2023 - Present · 1 yr 2 mos</p>
                            <p class="text-text dark:text-gray-300 text-sm leading-relaxed">
                                Assisting Dr. Smith in research related to Natural Language Processing. Curating datasets and running experiments using Python and PyTorch.
                            </p>
                        </div>
                        <div class="flex flex-col items-center gap-1 pt-1">
                            <div class="text-text bg-gray-100 dark:bg-[#2a3441] rounded-full p-1.5 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[20px]">school</span>
                            </div>
                        </div>
                        <div class="flex flex-1 flex-col pb-2">
                            <h4 class="text-title dark:text-white text-base font-bold leading-normal">CS 101 Tutor</h4>
                            <p class="text-title dark:text-gray-200 text-sm font-medium">State University · Student Job</p>
                            <p class="text-text dark:text-gray-400 text-xs font-normal mb-2">Sep 2022 - Dec 2022 · 4 mos</p>
                            <p class="text-text dark:text-gray-300 text-sm leading-relaxed">
                                Tutored 20+ freshman students in fundamental programming concepts including loops, conditionals, and data structures in Java.
                            </p>
                        </div>
                    </div>
                </section>
                <section class="bg-white dark:bg-[#1a222c] rounded-xl shadow-sm p-6 border border-borders">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-title dark:text-white">Volunteer & Leadership</h3>
                        <button class="text-text hover:text-primary transition-colors"><span class="material-symbols-outlined">add</span></button>
                    </div>
                    <div class="flex gap-4 mb-4 border-b border-borders dark:border-gray-700 pb-4 last:border-0 last:pb-0">
                        <div class="text-title dark:text-white flex items-center justify-center rounded-lg bg-gray-100 dark:bg-[#2a3441] shrink-0 size-12">
                            <span class="material-symbols-outlined">volunteer_activism</span>
                        </div>
                        <div class="flex flex-1 flex-col justify-center">
                            <h4 class="text-title dark:text-white text-base font-bold leading-normal">President</h4>
                            <p class="text-title dark:text-gray-200 text-sm font-normal">University Hackathon Club</p>
                            <p class="text-text dark:text-gray-400 text-xs mt-1">Aug 2023 - Present · Social Impact</p>
                        </div>
                    </div>
                </section>
                <section class="bg-white dark:bg-[#1a222c] rounded-xl shadow-sm p-6 border border-borders">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-title dark:text-white">Licenses & Certifications</h3>
                        <button class="text-text hover:text-primary transition-colors"><span class="material-symbols-outlined">add</span></button>
                    </div>
                    <div class="flex gap-4">
                        <div class="text-title dark:text-white flex items-center justify-center rounded-lg bg-gray-100 dark:bg-[#2a3441] shrink-0 size-12">
                            <span class="material-symbols-outlined">workspace_premium</span>
                        </div>
                        <div class="flex flex-1 flex-col justify-center">
                            <h4 class="text-title dark:text-white text-base font-bold leading-normal">AWS Certified Cloud Practitioner</h4>
                            <p class="text-title dark:text-gray-200 text-sm font-normal">Amazon Web Services (AWS)</p>
                            <p class="text-text dark:text-gray-400 text-xs mt-1">Issued Jul 2023 · Credential ID AWS-123456</p>
                            <button class="flex items-center gap-1 mt-2 text-primary text-sm font-medium hover:underline w-fit">
                                Show credential <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                            </button>
                        </div>
                    </div>
                </section>
                <section class="bg-white dark:bg-[#1a222c] rounded-xl shadow-sm p-6 border border-borders">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-title dark:text-white">Gigs / Freelance</h3>
                        <button class="text-text hover:text-primary transition-colors"><span class="material-symbols-outlined">add</span></button>
                    </div>
                    <div class="flex gap-4">
                        <div class="size-16 rounded-lg bg-gray-200 bg-cover bg-center shrink-0" data-alt="Screenshot of a local coffee shop website" style='background-image: url("https://lh3.googleusercontent.com/aida-public/AB6AXuDBNNcaHAa5Y2d7erqrek1LZAl7bWI7qEBkeqydEWR_HdY0l87cU2HS_EbJ8gPSBY_HwRDTLLhG3ReFCnMrFCpBBeuCGNwzizF2Ber826Qrc5tKSmzPc28CHcewSfgUodKMu9Tb7MqZDjjgbk-SXdzDCW5gbTTmlde6OYRPO4Dyo9nh6RYYYf6iBv2bT9FCh4t5R1lMzDXppMpbOrBcvgJEWoTLjdGDhTmPszvDvsZtEgG8WxUji6qOuYmR1Dji1LRrxtr8sSPKVa8");'></div>
                        <div class="flex flex-1 flex-col">
                            <h4 class="text-title dark:text-white text-base font-bold leading-normal">Local Coffee Shop Website</h4>
                            <p class="text-text dark:text-gray-400 text-sm font-normal mt-1">Designed and developed a responsive website for a local business using HTML, CSS, and JavaScript. Increased customer inquiries by 15%.</p>
                            <div class="flex gap-2 mt-2">
                                <a class="text-sm text-primary hover:underline font-medium" href="#">View Project</a>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <div class="lg:col-span-4 space-y-6">
                <section class="bg-white dark:bg-[#1a222c] rounded-xl shadow-sm p-6 border border-borders">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-title dark:text-white">Work Preference</h3>
                        <button class="text-text hover:text-primary"><span class="material-symbols-outlined text-[20px]">edit</span></button>
                    </div>
                    <div class="flex flex-col gap-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-text mt-0.5">apartment</span>
                            <div>
                                <p class="text-sm font-bold text-title dark:text-white">Remote, Hybrid, On-site</p>
                                <p class="text-xs text-text">Open to relocation</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-text mt-0.5">schedule</span>
                            <div>
                                <p class="text-sm font-bold text-title dark:text-white">Full-time, Internship</p>
                                <p class="text-xs text-text">Available starting May 2024</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-text mt-0.5">work_outline</span>
                            <div>
                                <p class="text-sm font-bold text-title dark:text-white">Roles</p>
                                <p class="text-xs text-text">Software Engineer, Web Developer, Data Analyst</p>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="bg-white dark:bg-[#1a222c] rounded-xl shadow-sm p-6 border border-borders">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-title dark:text-white">Skills</h3>
                        <div class="flex gap-2">
                            <button class="text-text hover:text-primary"><span class="material-symbols-outlined text-[20px]">add</span></button>
                            <button class="text-text hover:text-primary"><span class="material-symbols-outlined text-[20px]">edit</span></button>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-gray-100 dark:bg-[#2a3441] text-title dark:text-white text-sm font-medium rounded-full">Java</span>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-[#2a3441] text-title dark:text-white text-sm font-medium rounded-full">Python</span>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-[#2a3441] text-title dark:text-white text-sm font-medium rounded-full">React.js</span>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-[#2a3441] text-title dark:text-white text-sm font-medium rounded-full">Tailwind CSS</span>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-[#2a3441] text-title dark:text-white text-sm font-medium rounded-full">Git</span>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-[#2a3441] text-title dark:text-white text-sm font-medium rounded-full">SQL</span>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-[#2a3441] text-title dark:text-white text-sm font-medium rounded-full">Communication</span>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-[#2a3441] text-title dark:text-white text-sm font-medium rounded-full">Problem Solving</span>
                    </div>
                </section>
                <section class="bg-white dark:bg-[#1a222c] rounded-xl shadow-sm p-6 border border-borders">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-title dark:text-white">Languages</h3>
                        <button class="text-text hover:text-primary"><span class="material-symbols-outlined text-[20px]">edit</span></button>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-2 border-b border-borders dark:border-gray-700 last:border-0 last:pb-0">
                            <span class="text-title dark:text-white text-sm font-medium">English</span>
                            <span class="text-text dark:text-gray-400 text-sm">Native or Bilingual</span>
                        </div>
                        <div class="flex justify-between items-center pb-2 border-b border-borders dark:border-gray-700 last:border-0 last:pb-0">
                            <span class="text-title dark:text-white text-sm font-medium">Spanish</span>
                            <span class="text-text dark:text-gray-400 text-sm">Professional Working</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-title dark:text-white text-sm font-medium">French</span>
                            <span class="text-text dark:text-gray-400 text-sm">Elementary</span>
                        </div>
                    </div>
                </section>
                <section class="bg-white dark:bg-[#1a222c] rounded-xl shadow-sm p-6 border border-borders">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-title dark:text-white">Hobbies</h3>
                        <button class="text-text hover:text-primary"><span class="material-symbols-outlined text-[20px]">edit</span></button>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 border border-borders dark:border-gray-600 text-title dark:text-gray-300 text-sm rounded-full">Photography</span>
                        <span class="px-3 py-1 border border-borders dark:border-gray-600 text-title dark:text-gray-300 text-sm rounded-full">Hiking</span>
                        <span class="px-3 py-1 border border-borders dark:border-gray-600 text-title dark:text-gray-300 text-sm rounded-full">Gaming</span>
                        <span class="px-3 py-1 border border-borders dark:border-gray-600 text-title dark:text-gray-300 text-sm rounded-full">Chess</span>
                    </div>
                </section>
                <section class="bg-white dark:bg-[#1a222c] rounded-xl shadow-sm p-6 border border-borders">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-title dark:text-white">Social Links</h3>
                        <button class="text-text hover:text-primary"><span class="material-symbols-outlined text-[20px]">edit</span></button>
                    </div>
                    <div class="flex flex-col gap-3">
                        <a class="flex items-center gap-3 text-title dark:text-gray-200 hover:text-primary hover:bg-gray-50 dark:hover:bg-[#2a3441] p-2 -mx-2 rounded-lg transition-colors group" href="#">
                            <span class="text-2xl text-[#0a66c2] group-hover:text-primary transition-colors">
                                <svg fill="currentColor" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"></path></svg>
                            </span>
                            <span class="text-sm font-medium">/in/alex-johnson</span>
                            <span class="material-symbols-outlined ml-auto text-text text-[18px]">open_in_new</span>
                        </a>
                        <a class="flex items-center gap-3 text-title dark:text-gray-200 hover:text-primary hover:bg-gray-50 dark:hover:bg-[#2a3441] p-2 -mx-2 rounded-lg transition-colors group" href="#">
                            <span class="text-2xl text-title dark:text-white group-hover:text-primary transition-colors">
                                <svg fill="currentColor" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"></path></svg>
                            </span>
                            <span class="text-sm font-medium">/alexjohnson-dev</span>
                            <span class="material-symbols-outlined ml-auto text-text text-[18px]">open_in_new</span>
                        </a>
                        <a class="flex items-center gap-3 text-title dark:text-gray-200 hover:text-primary hover:bg-gray-50 dark:hover:bg-[#2a3441] p-2 -mx-2 rounded-lg transition-colors group" href="#">
                            <span class="material-symbols-outlined text-2xl text-primary2 group-hover:text-primary transition-colors">language</span>
                            <span class="text-sm font-medium">alexjohnson.io</span>
                            <span class="material-symbols-outlined ml-auto text-text text-[18px]">open_in_new</span>
                        </a>
                    </div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>

