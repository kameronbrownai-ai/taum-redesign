# TAUM website

Website for **Troy Area United Ministries**, a nonprofit neighborhood center at
392 Second Street, Troy, NY. Client contact is Rev. Abby Norton-Levering,
Executive Director. Built and maintained by Kameron Brown (freelance).

> **This repository is public.** Never commit SSH hosts, usernames, ports, keys,
> passwords, API keys, or client pricing. The deploy scripts are gitignored for
> exactly this reason.

## Where things are

```
site/          The original static HTML prototype. 17 pages, no build step.
               Still live as a preview. Source of truth for the design.
wp-theme/taum/ The WordPress theme, converted from site/. This is what ships.
wp-theme/build-import.py   Turns site/ into an import bundle.
wp-theme/import/import.php Creates pages, events, partners, posts, media, menu.
*.md           Planning docs: content inventory, brand palette, site copy, FAQ
               and AI-search notes, mission drafts.
```

Two deploy scripts live in the project root but are **gitignored** and exist only
on Kameron's Mac: `deploy-wp.sh` (WordPress theme) and `deploy-preview.sh`
(static site). They hold the server connection details.

## The two live sites

| | |
|---|---|
| WordPress (current) | `mediumslateblue-dolphin-566869.hostingersite.com` |
| Static preview (older) | `darkgrey-scorpion-632352.hostingersite.com` |

Both are temporary Hostinger subdomains. The real destination is **taum.org**,
which the client owns and which still runs their old site. Moving it needs a DNS
change on TAUM's side plus a `wp search-replace` of the URLs.

## Deploying

From Kameron's Mac only:

```bash
./deploy-wp.sh              # rsync the theme, lint PHP, purge cache, verify
./deploy-wp.sh --import     # ALSO re-run the content importer
```

`--import` re-applies the launch content. **Never run it on a site Abby has been
editing** — it would overwrite her work. It is for setting up a fresh install.

A cloud or mobile session can edit and push, but cannot deploy: the SSH key is
local. Push, then deploy from the Mac.

**Server gotcha:** the hosting account's `~/public_html` is a symlink to a
*different* website. Always use the full `domains/<site>/public_html` path. The
deploy script has a guard for this; keep it.

## How the theme works

The design is protected in PHP templates; the words are editable in WordPress.
That split is deliberate, so Abby can change text without breaking layout.

- **`front-page.php`** builds the homepage from the Customizer, sticky posts, and
  the program pages. No hardcoded copy.
- **`page.php`** renders any page: title, excerpt as the intro, featured image as
  the hero photo, plus `taum_kicker` and `taum_hero_style` meta. The body is the
  page content, imported as classic HTML with its layout classes intact.
- **`page-news.php`** renders Events and posts. **`page-about.php`** appends
  partners from the Partners post type.
- **`inc/customizer.php`** defines the **TAUM Info** panel: phone, address, hours,
  meal times, form links, homepage hero, campaign block, impact numbers. Read
  values with `taum_opt('key')`.
- **`inc/shortcodes.php`** gives page copy `[taum meal_monday]`, `[taum phone]`,
  `[taum tel ext="204"]`, so a fact changes in one place.
- **`inc/cpt.php`** registers `taum_event` (date, time, place, sign-up link,
  weekly flag) and `taum_partner` (tiered).
- **`inc/seo.php`** carries the schema.org work: Organization, FAQPage built by
  parsing the FAQ accordion, weekly Event schedules, plus `llms.txt` and AI
  crawler rules. This is a selling point of the project; do not drop it.
- **`inc/help.php`** is the in-admin guide for Abby and hides menus she does not
  need.

Adding a site-wide fact: add it to `taum_option_fields()`, then use `taum_opt()`
in a template or `[taum key]` in page copy. Do not hardcode.

## Conventions

- **No em dashes or en dashes in visible copy.** The client asked for this
  explicitly. Use commas, periods, parentheses, or colons. Hyphens in phone
  numbers, names like Norton-Levering, and compound words are fine.
- Match the existing voice: plain, warm, specific. "No ID, no referral, no
  questions asked," not "services are available to qualifying individuals."
- Never invent facts about the organization: no made-up statistics, donor names,
  event dates, or program details. Leave a marked placeholder and flag it.
- Escape output (`esc_html`, `esc_url`, `esc_attr`, `wp_kses_post`).
- Verify changes against the live site rather than assuming.

## Client separation

Kameron has another client, **Star Mat** (`starmat.app`, a separate repo and a
separate server). The two projects must never be visibly connected. Never
reference one from the other in code, docs, comments, or commit messages.

## Open items

Waiting on Abby:

- **MLK programming** — the page covers only the scholarship; she wants all the
  county-wide MLK programming. Materials not yet sent.
- **Teen Tech Center** — "Tech for Teens" may become a year-round "Teen Tech
  Center." Decision pending; appears in many files plus a URL.
- **Community Compass** — the card on the Community Center page is a placeholder
  written from the program name alone.
- **Sponsors** — the tier renders an empty-state box until real names arrive.

Project work:

- Move to taum.org when the client is ready.
- Optional: make the repo private and add a deploy Action so pushes deploy
  themselves, which would also allow deploying from mobile.
