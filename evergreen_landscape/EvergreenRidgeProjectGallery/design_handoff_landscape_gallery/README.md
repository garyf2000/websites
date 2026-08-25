# Handoff: Evergreen Ridge Landscaping — Project Gallery One-Pager

## Overview
A one-page marketing website for a landscaping company. The page shows a grid of recently completed projects; clicking a project card opens a detail overlay with the full scope, timeline, size, description, and a client testimonial. A sticky header carries the brand and a "Get a Quote" CTA; a contact strip closes the page.

## About the Design Files
The files in this bundle are **design references created in HTML** — prototypes showing intended look and behavior, not production code to copy directly. The task is to **recreate this design in the target codebase's existing environment** (React, Vue, etc.) using its established patterns and libraries. If no environment exists yet, choose an appropriate framework (a simple React or plain HTML/CSS build is sufficient — this is a static one-pager with one piece of client state).

Note: the prototype was styled with the Extreme Networks "EnReact" design-system tokens (dark theme, `--en-theme-*` CSS variables) because that system was bound to the design project. If the production site has its own brand, substitute the token values listed under Design Tokens; the layout and behavior are what to preserve.

## Fidelity
**High-fidelity** for layout, spacing, typography scale, and interactions. Colors are design-system tokens (dark UI theme) — treat them as placeholders if the company has its own brand palette. Project photos are drag-and-drop placeholders; production needs real images.

## Screens / Views

### 1. Header (sticky)
- 72px tall, full width, sticky top, z-index 10.
- Background: surface elevation-1; 1px bottom border (rgba(255,255,255,0.16)).
- Horizontal padding 40px; flex row, gap 24px.
- Left: 36×36px logo tile (8px radius, brand purple #7b4dff, white "E" 20px/700) + company name 20px/600.
- Right: text links "Projects", "Contact" (15px, anchor scroll), then primary button "Get a Quote".

### 2. Projects section (`#projects`)
- Max-width 1280px, centered, padding 56px 40px 80px; column, gap 32px.
- Intro block (max-width 720px): eyebrow "RECENT WORK" (13px, 2px letter-spacing, uppercase, subtle color), H1 40px/600 "Projects we finished this season", body 17px/1.6 subtle color.
- Card grid: `grid-template-columns: repeat(auto-fill, minmax(360px, 1fr))`, gap 24px. 6 cards.

#### Project card
- Surface elevation-1 background, 1px border rgba(255,255,255,0.16), 10px radius, overflow hidden, cursor pointer.
- Hover: translateY(-3px) + border color brand purple; transition 0.15s ease (transform, border-color).
- Image area: 220px tall (photo, cover fit).
- Body: padding 18px 20px 20px, column gap 10px.
  - Title 19px/600 with year right-aligned 13px subtle.
  - Location 14px subtle.
  - Tag pills: 12px text, 4px 10px padding, pill radius, 1px border, subtle color; flex wrap gap 8px.
- Whole card is the click target → opens detail overlay.

### 3. Detail overlay (per project)
- Backdrop: fixed inset 0, rgba(0,0,0,0.62), scrollable, padding 48px 24px; fade-in 0.15s. Click backdrop → close. Escape key → close.
- Panel: max-width 920px, elevation-1 background, 1px border, 12px radius; slide-up + fade 0.2s. Click inside does not close (stopPropagation).
- Hero image 340px tall with a close icon button top-right (16px inset).
- Content: padding 32px 36px 36px, column gap 24px.
  - Title 28px/600; location 15px subtle.
  - Meta stat tiles: grid `repeat(auto-fit, minmax(150px, 1fr))`, gap 16px. Each tile: 14px 16px padding, 1px border, 8px radius; label 12px uppercase 1px letter-spacing subtle; value 16px/600. Tiles: Completed / Duration / Size.
  - Description paragraph 16px/1.65.
  - "SCOPE OF WORK" label (13px uppercase) + two-column bullet grid `repeat(auto-fit, minmax(260px, 1fr))`, gap 8px 24px; bullets 15px/1.5 with accent-colored "•".
  - Testimonial: 2px left border in brand purple, 18px left padding; italic quote 16px/1.6; attribution 14px subtle prefixed with em dash.
  - Actions row, gap 12px: primary "Start a Similar Project", secondary "Close".

### 4. Contact strip (`#contact`)
- Top border, elevation-1 background; inner max-width 1280px, padding 48px 40px; flex row wrap, gap 32px, align center.
- Left: "Planning a project?" 24px/600 + support line 15px subtle.
- Middle: phone (555) 214-8830 and mailto link hello@evergreenridge.example.
- Right: primary button "Book a Consultation".

## Interactions & Behavior
- Card click → open that project's detail overlay.
- Close via: backdrop click, close icon button, "Close" button, or Escape key.
- Overlay animations: backdrop opacity 0→1 (0.15s ease); panel opacity 0→1 + translateY(16px→0) (0.2s ease).
- Card hover lift + border highlight (see card spec).
- Header anchor links scroll to #projects / #contact.
- Body scroll: overlay itself scrolls if content exceeds viewport.

## State Management
- `selectedProjectId: string | null` — the only client state.
- Global keydown listener for Escape while mounted.
- Optional flags used in the prototype: `showContact`, `showTestimonials` (both default true).
- No data fetching; project list is static content (see the .dc.html file for all six projects' full copy: titles, locations, years, durations, sizes, tags, descriptions, scope bullets, quotes, clients).

## Design Tokens
Dark theme. Prototype token → fallback value:
- Page background: `--en-theme-color-background-surface-elevation-0` (near-black)
- Card/panel/header background: `--en-theme-color-background-surface-elevation-1`
- Default text: `--en-theme-color-content-default` (near-white)
- Subtle text: rgba(255,255,255,0.65)
- Border: rgba(255,255,255,0.16)
- Brand/accent (buttons, hover border, testimonial rule): #7b4dff
- Link color: #7db8f0 (hover #a8d0f5, underline)
- Font: 'Source Sans Pro', sans-serif
- Radii: cards 10px, panel 12px, tiles 8px, pills 999px, logo 8px
- Spacing rhythm: 8 / 10 / 12 / 16 / 24 / 32 / 40 / 48 px

## Assets
- Project photos are included in `images/` (one per project, webp). In the prototype they live in drag-and-drop `<image-slot>` placeholders (ids `ph-<projectId>`); in production, render them as regular images (card 220px tall, detail hero 340px tall, cover-cropped):
- `images/hillcrest.webp` — photo for the "hillcrest" project (used on its card and detail hero)
- `images/maple.webp` — photo for the "maple" project (used on its card and detail hero)
- `images/lakeview.webp` — photo for the "lakeview" project (used on its card and detail hero)
- `images/cedar.webp` — photo for the "cedar" project (used on its card and detail hero)
- `images/stonegate.webp` — photo for the "stonegate" project (used on its card and detail hero)
- `images/willow.webp` — photo for the "willow" project (used on its card and detail hero)
- Buttons/close icon in the prototype are EnReact components (`ENButton`, `ENControlButton`, `ENIconClose`); use the target codebase's equivalents.

## Files
- `Landscape Projects.dc.html` — the full prototype: markup, inline styles, all six projects' content, and the interaction logic (bottom `Component` class).
