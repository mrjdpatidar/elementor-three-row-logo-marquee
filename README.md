# Three Row Logo Marquee — Elementor Multi-Row Brand Showcase

### Plugin Documentation & Developer Guide by Jagdish Patidar

A high-performance brand logo showcase widget for Elementor featuring three independent rows of partner logos, dual animation modes (smooth continuous marquee vs. synchronized stepped slides), monochrome grayscale filtering, and interactive hover states.

- **Developer:** Jagdish Patidar
- **Company:** [Drunall Technologies](https://drunall.com)
- **Project Type:** WordPress Plugin / Elementor Addon
- **Repository Type:** Open Source WordPress Plugin
- **Source Code:** Public
- **Tested Up To:** WordPress 6.x & Elementor 3.x

---

## Overview

Displaying client logos, media mentions, and technology partners is essential for establishing immediate trust on corporate landing pages. However, single-row tickers often feel sparse or repetitive when a company has 20+ logos to display.

**Three Row Logo Marquee** was engineered to display large volumes of client logos in an organized, visually engaging 3-row layout. It supports alternating movement directions, custom speeds, grayscale filters, and a synchronized stepped-slide animation mode where logos pause briefly before advancing together.

---

## The Business Problem

Standard logo carousel plugins suffer from notable limitations when building modern corporate hero or social proof sections:

- **Single Row Limitations:** Fitting 20 to 50 logos into a single ticker creates an excessively long loop where key brands are rarely seen.
- **Jerky Motion:** CSS marquee hacks frequently stutter or jump when images load asynchronously or on high-refresh-rate monitors.
- **Visual Clutter:** Multi-colored brand logos clash with website color palettes when unstyled.
- **Lack of Stepped Movement:** Many brands prefer stepped movements (sliding one logo at a time with a short pause) rather than continuous scrolling.

Three Row Logo Marquee resolves these problems with dedicated 3-row repeater controls, dual animation engines, and clean monochrome-to-color hover effects.

---

## My Contribution

I developed the plugin to combine visual impact with robust performance and responsive flexibility:

- Built separate repeater controls for Row 1, Row 2, and Row 3, enabling individual logo lists, links, alt attributes, and independent flow directions (Left-to-Right or Right-to-Left).
- Engineered dual animation engines in JavaScript:
  - **Smooth Marquee:** Continuous fluid movement driven by a requestAnimationFrame loop.
  - **Synchronized Step Slide:** A master synchronization timer that coordinates all three rows to slide by one logo unit simultaneously and pause cleanly.
- Implemented responsive visible logo counters (Desktop, Tablet, Mobile) driven by CSS custom properties.
- Added automatic image duplicate prevention within rows to ensure seamless looping without visual glitches.
- Built-in grayscale CSS filter with color reveal and motion pause on cursor hover.

---

## Key Features

- **Three Independent Rows:**
  - Dedicated repeaters for Row 1, Row 2, and Row 3.
  - Configurable flow direction per row (alternating rows create a dynamic zig-zag motion).
  - Custom logo upload, URL link, and descriptive `alt` text.
  - Automatic duplicate prevention within row loops.

- **Dual Animation Modes:**
  - **Smooth Marquee:** Constant fluid velocity with customizable duration in seconds.
  - **Slide (Step-by-Step):** Synchronized clock where all rows step forward together, with customizable motion vs. pause fractions.

- **Interactive Hover & Visual Filters:**
  - **Grayscale by Default:** Converts all logos into cohesive monochrome tones.
  - **Color on Hover:** Restores authentic brand colors on mouse hover.
  - **Pause on Hover:** Halts marquee animation when interacting with a specific logo.
  - Optional "Open links in new tab" (`_blank`) support.

- **Responsive Viewport Controls:**
  - Responsive logo count sliders (Desktop: 6, Tablet: 4, Mobile: 2 by default).
  - Sliders for logo max height, horizontal gap, vertical row gap, and logo inner padding.
  - Background color and section padding controls.

---

## Technical Architecture

```text
three-row-logo-marquee/
├── three-row-logo-marquee.php    # Plugin bootstrap, Elementor check & script enqueueing
├── widget.php                   # Elementor Widget Base class, controls & HTML rendering
└── assets/
    ├── style.css                # Layout styles, CSS grid/flex, grayscale & hover filters
    └── marquee.js               # Dual animation engine (smooth ticker & stepped synchronization)
```

- **Hooks Used:** `plugins_loaded`, `elementor/widgets/register`, `wp_enqueue_scripts`
- **Standards:** Sanitized via `esc_url`, `esc_attr`, and strictly typed integers.

---

## Installation & Setup

1. Compress the `three-row-logo-marquee` folder into `three-row-logo-marquee.zip`.
2. In WordPress Admin, navigate to **Plugins > Add New > Upload Plugin**.
3. Upload the zip file, click **Install Now**, and click **Activate**.
4. In Elementor, search for **3 Row Logo Marquee**.
5. Drag onto your page and populate your partner logos across the three rows.

---

## Author & License

- **Developer:** Jagdish Patidar
- **Company:** Drunall Technologies
- **License:** GPL-2.0-or-later
