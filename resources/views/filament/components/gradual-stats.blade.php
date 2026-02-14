{{-- SARH v1.9 — Gradual Stats Counter Animation --}}
{{-- When stat values change (via polling), animate the transition gradually over ~60 seconds --}}
<script>
(function() {
    'use strict';

    const ANIMATION_DURATION = 60000; // 60 seconds minimum
    const FRAME_INTERVAL = 200;       // update every 200ms for smooth counting
    const TOTAL_STEPS = ANIMATION_DURATION / FRAME_INTERVAL; // 300 steps

    // Store previous values and active animations
    const prevValues = new Map();
    const activeAnimations = new Map();

    /**
     * Extract numeric value from stat text (handles Arabic numerals, commas, decimals)
     */
    function extractNumber(text) {
        if (!text) return null;
        // Convert Arabic/Persian numerals to Western
        let cleaned = text.replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                         .replace(/[۰-۹]/g, d => String.fromCharCode(d.charCodeAt(0) - 1728));
        // Remove thousands separators and keep decimal point
        cleaned = cleaned.replace(/,/g, '');
        // Extract the first number (integer or decimal)
        const match = cleaned.match(/-?[\d]+\.?[\d]*/);
        return match ? parseFloat(match[0]) : null;
    }

    /**
     * Format number back to display format (with commas, matching original decimal places)
     */
    function formatNumber(num, decimalPlaces) {
        const parts = num.toFixed(decimalPlaces).split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return parts.join('.');
    }

    /**
     * Get decimal places from original text
     */
    function getDecimalPlaces(text) {
        const cleaned = text.replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                           .replace(/[۰-۹]/g, d => String.fromCharCode(d.charCodeAt(0) - 1728))
                           .replace(/,/g, '');
        const match = cleaned.match(/-?[\d]+\.(\d+)/);
        return match ? match[1].length : 0;
    }

    /**
     * Easing function — ease-out-cubic for natural deceleration
     */
    function easeOutCubic(t) {
        return 1 - Math.pow(1 - t, 3);
    }

    /**
     * Animate a stat element's value from old to new
     */
    function animateStatValue(el, oldVal, newVal, suffix, decimalPlaces) {
        const key = el.dataset.sarhStatId;

        // Cancel any existing animation on this element
        if (activeAnimations.has(key)) {
            cancelAnimationFrame(activeAnimations.get(key));
            activeAnimations.delete(key);
        }

        const startTime = performance.now();
        const diff = newVal - oldVal;

        // Add a subtle pulse class
        el.closest('.fi-wi-stats-overview-stat')?.classList.add('sarh-stat-updating');

        function step(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / ANIMATION_DURATION, 1);
            const easedProgress = easeOutCubic(progress);

            const currentVal = oldVal + (diff * easedProgress);
            const formattedVal = formatNumber(currentVal, decimalPlaces);

            // Rebuild the text with suffix
            el.textContent = suffix ? formattedVal + ' ' + suffix : formattedVal;

            if (progress < 1) {
                activeAnimations.set(key, requestAnimationFrame(step));
            } else {
                activeAnimations.delete(key);
                el.closest('.fi-wi-stats-overview-stat')?.classList.remove('sarh-stat-updating');
                // Store final value
                prevValues.set(key, newVal);
            }
        }

        activeAnimations.set(key, requestAnimationFrame(step));
    }

    /**
     * Process all stat elements, detect changes, animate
     */
    function processStats() {
        // Filament stats value: the <dd> inside .fi-wi-stats-overview-stat
        const statCards = document.querySelectorAll('.fi-wi-stats-overview-stat');

        statCards.forEach((card, idx) => {
            // The value is in the <dd> element or a specific text container
            const valueEl = card.querySelector('dd, [class*="text-3xl"], [class*="text-2xl"]');
            if (!valueEl) return;

            // Assign a unique ID if not present
            if (!valueEl.dataset.sarhStatId) {
                valueEl.dataset.sarhStatId = 'stat-' + idx + '-' + Math.random().toString(36).substr(2, 5);
            }

            const key = valueEl.dataset.sarhStatId;
            const rawText = valueEl.textContent.trim();
            const numericVal = extractNumber(rawText);

            if (numericVal === null) return; // non-numeric stat (like '—')

            const decimalPlaces = getDecimalPlaces(rawText);

            // Extract the suffix (ر.س, حالة, etc.)
            const cleaned = rawText.replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                                   .replace(/[۰-۹]/g, d => String.fromCharCode(d.charCodeAt(0) - 1728))
                                   .replace(/,/g, '');
            const numMatch = cleaned.match(/-?[\d]+\.?[\d]*/);
            let suffix = '';
            if (numMatch) {
                suffix = rawText.substring(rawText.indexOf(numMatch[0]) + numMatch[0].length).trim();
                // Also check if suffix is before the number by looking at the raw text
                if (!suffix) {
                    const beforeNum = rawText.substring(0, rawText.indexOf(numMatch[0])).trim();
                    if (beforeNum) suffix = ''; // number is the main content
                }
            }

            if (!prevValues.has(key)) {
                // First time seeing this stat — store value, no animation
                prevValues.set(key, numericVal);
                return;
            }

            const prevVal = prevValues.get(key);

            // If value changed, animate
            if (Math.abs(prevVal - numericVal) > 0.001) {
                // Prevent Filament from showing final value immediately
                // Animate from previous to new
                animateStatValue(valueEl, prevVal, numericVal, suffix, decimalPlaces);
            }
        });
    }

    // Observe DOM changes to detect when Filament polling updates stats
    let debounceTimer = null;
    const observer = new MutationObserver(function(mutations) {
        let hasStatChange = false;
        for (const m of mutations) {
            if (m.target.closest && m.target.closest('.fi-wi-stats-overview-stat')) {
                hasStatChange = true;
                break;
            }
            // Check added nodes
            for (const node of m.addedNodes) {
                if (node.nodeType === 1 && (
                    node.classList?.contains('fi-wi-stats-overview-stat') ||
                    node.querySelector?.('.fi-wi-stats-overview-stat')
                )) {
                    hasStatChange = true;
                    break;
                }
            }
            if (hasStatChange) break;
        }

        if (hasStatChange) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(processStats, 100);
        }
    });

    function init() {
        // Initial scan (store values, no animation)
        processStats();

        // Start observing
        observer.observe(document.body, {
            childList: true,
            subtree: true,
            characterData: true,
        });
    }

    // Run on page load and SPA navigation
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    document.addEventListener('livewire:navigated', function() {
        // Reset stored values on page navigation
        prevValues.clear();
        activeAnimations.forEach(id => cancelAnimationFrame(id));
        activeAnimations.clear();
        setTimeout(init, 500);
    });
})();
</script>

<style>
/* Subtle pulse glow while stat is animating */
.sarh-stat-updating {
    position: relative;
}
.sarh-stat-updating::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: inherit;
    pointer-events: none;
    animation: sarhStatPulse 2s ease-in-out infinite;
    border: 2px solid transparent;
}

@keyframes sarhStatPulse {
    0%, 100% {
        border-color: transparent;
        box-shadow: none;
    }
    50% {
        border-color: rgba(255, 140, 0, 0.3);
        box-shadow: 0 0 12px rgba(255, 140, 0, 0.1);
    }
}

/* Smooth transition for stat text changes */
.fi-wi-stats-overview-stat dd,
.fi-wi-stats-overview-stat [class*="text-3xl"],
.fi-wi-stats-overview-stat [class*="text-2xl"] {
    transition: color 0.5s ease;
}
</style>
