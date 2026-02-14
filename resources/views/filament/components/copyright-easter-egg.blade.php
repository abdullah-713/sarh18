{{-- SARH — Copyright Easter Egg (Owner Credentials Trigger) --}}
<div
    x-data="{
        show: false,
        phase: 0,
        particles: [],
        init() {
            this.generateParticles();

            // Watch for login page and intercept
            this.$nextTick(() => this.attachLoginListener());

            // Re-attach on SPA navigation
            document.addEventListener('livewire:navigated', () => {
                setTimeout(() => this.attachLoginListener(), 300);
            });
        },
        attachLoginListener() {
            const form = document.querySelector('.fi-simple-page form');
            if (!form || form._crAttached) return;
            form._crAttached = true;

            // Make email field accept any text (change type from email to text)
            const emailInput = form.querySelector('input[type=email]');
            if (emailInput) {
                emailInput.addEventListener('input', (e) => {
                    const v = e.target.value;
                    if (/[\u0600-\u06FF]/.test(v)) {
                        emailInput.type = 'text';
                    }
                });
            }

            form.addEventListener('submit', (e) => {
                const emailEl = form.querySelector('input[type=email], input[type=text][autocomplete*=username], input[wire\\:model\\.defer*=email], input[id*=email]');
                const passEl = form.querySelector('input[type=password]');
                if (!emailEl || !passEl) return;
                const email = emailEl.value.trim();
                const pass = passEl.value.trim();
                if (email === '\u0645\u0627\u0644\u0643 \u0627\u0644\u062A\u0637\u0628\u064A\u0642' && pass === 'carsspace88@gmail.com') {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    this.triggerAnimation();
                    return false;
                }
            }, true);
        },
        generateParticles() {
            for (let i = 0; i < 60; i++) {
                this.particles.push({
                    x: Math.random() * 100,
                    y: Math.random() * 100,
                    size: Math.random() * 4 + 1,
                    delay: Math.random() * 3,
                    duration: Math.random() * 4 + 3,
                    opacity: Math.random() * 0.6 + 0.2,
                });
            }
        },
        async triggerAnimation() {
            this.show = true;
            this.phase = 1;
            await this.wait(600);
            this.phase = 2;
            await this.wait(800);
            this.phase = 3;
            await this.wait(600);
            this.phase = 4;
            await this.wait(600);
            this.phase = 5;
            await this.wait(600);
            this.phase = 6;
        },
        wait(ms) {
            return new Promise(r => setTimeout(r, ms));
        },
        close() {
            this.phase = 0;
            setTimeout(() => { this.show = false; }, 600);
        }
    }"
    x-show="show"
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-500"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-cloak
    @click="close()"
    style="display: none; z-index: 99999;"
    class="fixed inset-0 flex items-center justify-center"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/95 backdrop-blur-md"></div>

    {{-- Floating particles --}}
    <template x-for="(p, i) in particles" :key="i">
        <div
            class="absolute rounded-full pointer-events-none"
            :class="i % 3 === 0 ? 'cr-particle-gold' : (i % 3 === 1 ? 'cr-particle-orange' : 'cr-particle-white')"
            :style="`
                left: ${p.x}%;
                top: ${p.y}%;
                width: ${p.size}px;
                height: ${p.size}px;
                opacity: ${p.opacity};
                animation: crFloat ${p.duration}s ease-in-out ${p.delay}s infinite alternate;
            `"
        ></div>
    </template>

    {{-- Main content --}}
    <div class="relative z-10 max-w-2xl w-full mx-4 text-center select-none" dir="rtl">

        {{-- Decorative top line --}}
        <div
            class="mx-auto mb-8 overflow-hidden"
            :class="phase >= 1 ? 'cr-line-reveal' : 'opacity-0'"
        >
            <div class="h-px bg-gradient-to-r from-transparent via-amber-400 to-transparent"></div>
            <div class="flex items-center justify-center gap-3 mt-3">
                <div class="w-8 h-px bg-amber-500/60"></div>
                <svg class="w-6 h-6 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 1L9 9H1l6.5 5-2.5 8L12 17l7 5-2.5-8L23 9h-8z"/>
                </svg>
                <div class="w-8 h-px bg-amber-500/60"></div>
            </div>
        </div>

        {{-- Shield / Seal icon --}}
        <div
            class="mx-auto mb-6"
            :class="phase >= 2 ? 'cr-seal-appear' : 'opacity-0 scale-0'"
        >
            <div class="w-24 h-24 mx-auto rounded-full border-2 border-amber-400/50 flex items-center justify-center cr-seal-glow">
                <div class="w-20 h-20 rounded-full border border-amber-500/30 flex items-center justify-center bg-amber-500/5">
                    <svg class="w-10 h-10 text-amber-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Arabic title --}}
        <h2
            class="text-2xl md:text-3xl font-black mb-3 leading-relaxed"
            style="font-family: 'Cairo', sans-serif; color: #FFB347;"
            :class="phase >= 3 ? 'cr-text-reveal' : 'opacity-0 translate-y-4'"
        >
            إشعار حقوق الملكية الفكرية
        </h2>

        {{-- Arabic body --}}
        <p
            class="text-base md:text-lg leading-loose mb-2"
            style="font-family: 'Cairo', sans-serif; color: #e5e5e5;"
            :class="phase >= 3 ? 'cr-text-reveal-delay1' : 'opacity-0 translate-y-4'"
        >
            جميع الحقوق الفكرية لجميع ملفات الكود بلا استثناء
            <br>
            تعود ملكيتها بشكل
            <span class="font-black" style="color: #FFD700;">حصري وشخصي وفردي</span>
            <br>
            للسيد
        </p>

        {{-- Owner name Arabic --}}
        <h1
            class="text-3xl md:text-4xl font-black mb-4 leading-relaxed"
            style="font-family: 'Cairo', sans-serif;"
            :class="phase >= 4 ? 'cr-name-glow' : 'opacity-0 scale-95'"
        >
            <span class="cr-golden-text">عبدالحكيم خلف المذهول</span>
        </h1>

        {{-- Arabic warning --}}
        <p
            class="text-sm md:text-base mb-6"
            style="font-family: 'Cairo', sans-serif; color: #f87171;"
            :class="phase >= 4 ? 'cr-text-reveal-delay2' : 'opacity-0 translate-y-4'"
        >
            ⛔ يُمنع النسخ أو الاستخدام كلياً أو جزئياً إلا بإذن خطي من المالك
        </p>

        {{-- Divider --}}
        <div
            class="mx-auto mb-6 w-48"
            :class="phase >= 5 ? 'cr-line-reveal' : 'opacity-0'"
        >
            <div class="h-px bg-gradient-to-r from-transparent via-gray-500 to-transparent"></div>
        </div>

        {{-- English title --}}
        <h3
            class="text-lg md:text-xl font-bold mb-2 tracking-wide"
            style="color: #FFB347;"
            dir="ltr"
            :class="phase >= 5 ? 'cr-text-reveal' : 'opacity-0 translate-y-4'"
        >
            INTELLECTUAL PROPERTY NOTICE
        </h3>

        {{-- English body --}}
        <p
            class="text-sm md:text-base leading-relaxed mb-2"
            style="color: #d4d4d4;"
            dir="ltr"
            :class="phase >= 5 ? 'cr-text-reveal-delay1' : 'opacity-0 translate-y-4'"
        >
            All intellectual property rights for all code files, without exception,
            <br>
            are <span class="font-bold" style="color: #FFD700;">exclusively, personally, and individually</span> owned by
        </p>

        {{-- Owner name English --}}
        <h2
            class="text-2xl md:text-3xl font-black mb-3 tracking-wider"
            dir="ltr"
            :class="phase >= 6 ? 'cr-name-glow' : 'opacity-0 scale-95'"
        >
            <span class="cr-golden-text">Abdulhakim Khalaf Al-Mathhoul</span>
        </h2>

        {{-- English warning --}}
        <p
            class="text-xs md:text-sm mb-8"
            style="color: #f87171;"
            dir="ltr"
            :class="phase >= 6 ? 'cr-text-reveal-delay2' : 'opacity-0 translate-y-4'"
        >
            ⛔ Copying or use, in whole or in part, is strictly prohibited without written permission from the owner.
        </p>

        {{-- Decorative bottom line --}}
        <div
            class="mx-auto overflow-hidden"
            :class="phase >= 6 ? 'cr-line-reveal' : 'opacity-0'"
        >
            <div class="flex items-center justify-center gap-3 mb-3">
                <div class="w-8 h-px bg-amber-500/60"></div>
                <svg class="w-5 h-5 text-amber-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 1L9 9H1l6.5 5-2.5 8L12 17l7 5-2.5-8L23 9h-8z"/>
                </svg>
                <div class="w-8 h-px bg-amber-500/60"></div>
            </div>
            <div class="h-px bg-gradient-to-r from-transparent via-amber-400 to-transparent"></div>
        </div>

        {{-- Tap to close --}}
        <p
            class="mt-6 text-xs animate-pulse"
            style="color: #737373;"
            :class="phase >= 6 ? 'cr-text-reveal-delay2' : 'opacity-0'"
        >
            اضغط في أي مكان للإغلاق &nbsp;|&nbsp; Tap anywhere to close
        </p>
    </div>
</div>

<style>
    /* Golden text gradient */
    .cr-golden-text {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 25%, #FFD700 50%, #FFEC8B 75%, #FFD700 100%);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        animation: crShimmer 3s linear infinite;
    }

    /* Particles */
    .cr-particle-gold { background: #FFD700; }
    .cr-particle-orange { background: #FF8C00; }
    .cr-particle-white { background: rgba(255, 255, 255, 0.8); }

    @keyframes crFloat {
        0% { transform: translateY(0) translateX(0); }
        100% { transform: translateY(-20px) translateX(10px); }
    }

    @keyframes crShimmer {
        0% { background-position: 0% center; }
        100% { background-position: 200% center; }
    }

    /* Seal glow */
    .cr-seal-glow {
        box-shadow: 0 0 30px rgba(255, 215, 0, 0.15), 0 0 60px rgba(255, 140, 0, 0.08);
        animation: crPulseGlow 2.5s ease-in-out infinite alternate;
    }

    @keyframes crPulseGlow {
        0% { box-shadow: 0 0 20px rgba(255, 215, 0, 0.1), 0 0 40px rgba(255, 140, 0, 0.05); }
        100% { box-shadow: 0 0 40px rgba(255, 215, 0, 0.2), 0 0 80px rgba(255, 140, 0, 0.1); }
    }

    /* Seal appear */
    .cr-seal-appear {
        animation: crSealIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }

    @keyframes crSealIn {
        0% { opacity: 0; transform: scale(0) rotate(-180deg); }
        100% { opacity: 1; transform: scale(1) rotate(0deg); }
    }

    /* Line reveal */
    .cr-line-reveal {
        animation: crLineIn 0.8s cubic-bezier(0.22, 0.61, 0.36, 1) both;
    }

    @keyframes crLineIn {
        0% { opacity: 0; transform: scaleX(0); }
        100% { opacity: 1; transform: scaleX(1); }
    }

    /* Text reveals */
    .cr-text-reveal {
        animation: crTextUp 0.6s cubic-bezier(0.22, 0.61, 0.36, 1) both;
    }

    .cr-text-reveal-delay1 {
        animation: crTextUp 0.6s cubic-bezier(0.22, 0.61, 0.36, 1) 0.2s both;
    }

    .cr-text-reveal-delay2 {
        animation: crTextUp 0.6s cubic-bezier(0.22, 0.61, 0.36, 1) 0.4s both;
    }

    @keyframes crTextUp {
        0% { opacity: 0; transform: translateY(16px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    /* Name glow */
    .cr-name-glow {
        animation: crNameIn 0.8s cubic-bezier(0.22, 0.61, 0.36, 1) both;
    }

    @keyframes crNameIn {
        0% { opacity: 0; transform: scale(0.9); filter: blur(8px); }
        50% { opacity: 1; filter: blur(0); }
        100% { opacity: 1; transform: scale(1); filter: blur(0); }
    }
</style>
