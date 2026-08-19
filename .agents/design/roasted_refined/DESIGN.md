---
name: Roasted & Refined
colors:
  surface: '#fcf9f8'
  surface-dim: '#dcd9d9'
  surface-bright: '#fcf9f8'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f6f3f2'
  surface-container: '#f0eded'
  surface-container-high: '#eae7e7'
  surface-container-highest: '#e5e2e1'
  on-surface: '#1b1c1c'
  on-surface-variant: '#504442'
  inverse-surface: '#303030'
  inverse-on-surface: '#f3f0ef'
  outline: '#827472'
  outline-variant: '#d3c3c0'
  surface-tint: '#745853'
  primary: '#271310'
  on-primary: '#ffffff'
  primary-container: '#3e2723'
  on-primary-container: '#ae8d87'
  inverse-primary: '#e3beb8'
  secondary: '#655d5a'
  on-secondary: '#ffffff'
  secondary-container: '#ece0dc'
  on-secondary-container: '#6b6360'
  tertiary: '#001e05'
  on-tertiary: '#ffffff'
  tertiary-container: '#00350f'
  on-tertiary-container: '#5fa364'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffdad4'
  primary-fixed-dim: '#e3beb8'
  on-primary-fixed: '#2b1613'
  on-primary-fixed-variant: '#5b403c'
  secondary-fixed: '#ece0dc'
  secondary-fixed-dim: '#cfc4c0'
  on-secondary-fixed: '#201a18'
  on-secondary-fixed-variant: '#4c4542'
  tertiary-fixed: '#abf4ac'
  tertiary-fixed-dim: '#90d792'
  on-tertiary-fixed: '#002107'
  on-tertiary-fixed-variant: '#07521d'
  background: '#fcf9f8'
  on-background: '#1b1c1c'
  surface-variant: '#e5e2e1'
typography:
  headline-xl:
    fontFamily: Playfair Display
    fontSize: 48px
    fontWeight: '700'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Playfair Display
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
  headline-lg-mobile:
    fontFamily: Playfair Display
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
  headline-md:
    fontFamily: Playfair Display
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-bold:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  container-padding-mobile: 16px
  container-padding-desktop: 48px
  gutter: 24px
  section-gap: 80px
---

## Brand & Style
The design system embodies a "Warm Professionalism" aesthetic, tailored for a high-end coffee experience that feels both artisanal and technologically seamless. The brand personality is grounded, welcoming, and sophisticated, targeting urban professionals and coffee enthusiasts who value quality and efficiency.

The visual style is a blend of **Modern Minimalism** and **Tactile Sophistication**. It prioritizes heavy whitespace to evoke a sense of calm, paired with high-quality photography that highlights the textures of steam, crema, and organic materials. UI elements use subtle depth to feel approachable and physical, moving away from flat "app-like" sterility toward a digital extension of a physical boutique cafe.

## Colors
The palette is rooted in the earthy tones of the coffee brewing process. 
- **Primary (Deep Roasted Brown):** Used for primary headings, navigation backgrounds, and high-impact brand moments.
- **Secondary (Warm Latte):** Acts as the primary surface color for cards and containers, providing a soft alternative to pure white.
- **Tertiary (Sage Green):** Reserved for organic indicators, "In Stock" statuses, and sustainability messaging.
- **Deep Espresso:** Used for body text and icon strokes to ensure high legibility and a grounded feel.
- **Burnt Orange:** A high-visibility accent color used exclusively for Call-to-Action (CTA) buttons, notifications, and urgent alerts.

The background is a subtle off-white (#FCFBF9) to reduce eye strain and enhance the warmth of the Latte and Sage tones.

## Typography
The typography strategy creates a high-contrast hierarchy between the artisanal Serif and the utilitarian Sans-serif.

**Playfair Display** is used for all major headings. It should be typeset with slightly tighter letter-spacing in larger formats to maintain a premium, editorial look.

**Inter** handles all functional text. It is chosen for its exceptional legibility on digital screens, particularly for menu items, pricing, and POS interface elements. Label styles should use uppercase with increased tracking to differentiate functional UI labels from narrative body text.

## Layout & Spacing
The layout follows a **Fluid Grid** model with a focus on generous internal padding to reflect a "premium" space.

- **Desktop:** 12-column grid with 24px gutters. Content is often centered with wide margins to create an editorial feel.
- **Mobile:** 4-column grid with 16px margins. 
- **Rhythm:** All spacing increments are multiples of 8px. Use 80px or 120px gaps between major vertical sections to maintain a sense of openness.

UI elements should never feel crowded. If in doubt, increase the white space around an element to elevate its perceived value.

## Elevation & Depth
This design system uses **Tonal Layering** combined with **Ambient Shadows**. 

Depth is primarily communicated by placing Warm Latte (#D7CCC8) or White containers on top of the slightly darker off-white background. Shadows should be extremely soft: use a large blur radius (16px-24px) with very low opacity (5-10%) and a slight tint of the Primary color (#3E2723) rather than pure black. This creates a "natural light" effect as if the UI elements are sitting on a wooden or marble cafe table.

Avoid heavy inner shadows or glows. Use a subtle 1px border in a slightly darker shade of the surface color to define boundaries without adding visual noise.

## Shapes
The shape language is defined by "Soft Geometry." All primary containers, buttons, and input fields utilize a **12px (0.75rem)** corner radius. 

- **Cards/Buttons:** 12px corner radius.
- **Large Sections/Feature Images:** 24px corner radius.
- **Status Badges/Chips:** Fully pill-shaped to contrast against the structured squareness of the primary cards.

Iconography should utilize thin (1.5px or 2px) strokes with rounded caps and joins to mirror the friendliness of the corner radius.

## Components
### Buttons
- **Primary:** Burnt Orange background, White text. High-contrast for conversion.
- **Secondary:** Transparent with a 2px stroke of Deep Roasted Brown. 
- **POS Buttons:** Larger hit targets (min 56px height) with a subtle lift effect on hover.

### Cards
Cards are the core of the menu experience. They feature a clean top-aligned image with a 12px radius, followed by the Playfair Display product title. On hover, cards should subtly scale up (1.02x) and the shadow should deepen slightly.

### Status Badges
Used for order tracking.
- **Pending:** Warm Latte background, Deep Roasted Brown text.
- **Processing:** Light Sage background, Darker Sage text.
- **Completed:** Deep Espresso background, White text.

### Input Fields
Inputs use the Warm Latte color for the background with a subtle Deep Roasted Brown bottom border (2px) to give them a modern, structured look while remaining touch-friendly.

### Navigation
The navigation bar is elegant and slim. On scroll, it transitions from transparent to a semi-transparent Deep Roasted Brown with a backdrop blur (Glassmorphism) to keep the focus on the content while maintaining accessibility.