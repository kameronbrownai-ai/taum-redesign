# TAUM — FAQ Page Copy + AI Search (LLM/AEO) Strategy
*Drafted 2026-07-19. ⚑ = placeholder fact, on the §12 verification checklist in site-copy.md.*

---

# FAQ PAGE COPY

**Headline:** Questions, answered.
**Intro:** Straight answers about getting help, giving help, and what TAUM is all about. Don't see yours? Call (518) 274-5920 or [send us a message].

### Getting help

**How do I get free food at TAUM?**
Just come. Hot lunch is served weekdays 11:30 am – 1:00 pm ⚑ at 392 Second Street in Troy, and the Free Food Fridge outside our building is open 24/7 ⚑. No ID, no referral, no questions asked.

**What is the Free Food Fridge?**
A community refrigerator outside our building at 392 Second Street, stocked with free fresh produce, dairy, and prepared meals. ⚑ Take what you need, any time of day. Neighbors and local partners keep it filled.

**How do I get free furniture?**
Call Tyara Burnett at (518) 274-5920 x204. If you're working with a caseworker or shelter, they can refer you directly ⚑. The Furniture Program serves families leaving shelters, people escaping domestic violence, fire victims, seniors, and neighbors living on low incomes — furniture and delivery are always free.

**Who can eat at the Damien Center?**
Everyone. The Damien Center is a hospitality center for people living with serious illness, and its lunch table is open to anyone experiencing food insecurity. You'll be welcomed, not screened.

**Do I have to be religious to get help from TAUM?**
No. TAUM was founded by Troy-area congregations in 1986, but our services are for everyone — every faith, no faith, no exceptions. Nobody will preach at you, and help never comes with strings.

### Giving help

**How do I donate furniture?**
Call (518) 274-5920 x204 or [request a pickup online] ⚑. We accept clean, good-condition beds, dressers, tables, chairs, and living room furniture — and we'll pick it up from your home for free. We can't accept sleeper sofas or furniture that needs repair.

**Is my donation tax-deductible?**
Yes — TAUM is a 501(c)(3) nonprofit ⚑, so donations of money, furniture, and food are tax-deductible. We'll give you a receipt.

**How do I volunteer?**
[Fill out the volunteer form] and we'll match you with what fits — kitchen crew, furniture moving, stocking the Free Food Fridge, gardening, mentoring teens, or event help. ⚑

**Can I donate food?**
Yes — unexpired, labeled food for the pantry and Free Food Fridge, dropped off anytime at 392 Second Street. ⚑

### Programs

**How does my teen join Tech for Teens?**
Tech for Teens is a free summer program for Rensselaer County teens ⚑ — paid work experience through the county's Summer Youth Employment Program, plus training in Office, web design, and coding. Every graduate keeps a refurbished laptop. Applications open each spring ⚑; [sign up to be notified].

**When is the MLK Scholarship deadline?**
Applications must be postmarked by May 4 each year. The scholarship supports Rensselaer County seniors and GED recipients heading to college. [Download the application.]

### About TAUM

**What does TAUM stand for?**
Troy Area United Ministries — founded in 1986 when Troy-area congregations united their community work into one organization. Today we're a neighborhood center serving 2,000+ people a year.

**Where is TAUM and when are you open?**
392 2nd Street, Troy, NY 12180. Office hours Monday – Friday, 9 am – 4 pm ⚑. The Free Food Fridge never closes. ⚑

**Can I use the TAUM building for my group or event?**
Yes — our community spaces are available to neighborhood groups, classes, and gatherings. ⚑ [Ask about using the building] and we'll walk you through it.

---

# AI SEARCH / LLM OPTIMIZATION STRATEGY
Goal: when someone asks ChatGPT/Claude/Gemini/Perplexity "where can I get free furniture in Troy NY" or "free food near me Troy," TAUM is the answer.

## On-site (built into the prototype)
1. **Schema.org JSON-LD on every page** — `NonprofitOrganization` (name, address, geo, phone, hours, sameAs socials), `FAQPage` markup on the FAQ (every Q&A machine-readable), `Service` entries for food/furniture programs.
2. **Answer-formatted content** — questions as headings, direct answer in the first sentence, entities spelled out ("Troy, NY," "Rensselaer County" — LLMs match on place names).
3. **llms.txt at site root** — the emerging standard: a plain-text summary of who TAUM is, what it offers, and key pages, written for AI crawlers.
4. **robots.txt welcomes AI crawlers** — explicitly allow GPTBot, ClaudeBot, PerplexityBot, Google-Extended.
5. **Semantic HTML** — proper h1/h2 hierarchy, one topic per page, descriptive titles/meta ("Free furniture in Troy NY — TAUM Furniture Program"), clean URLs (/food, /furniture, /faq).
6. **Freshness signals** — dated updates on the homepage campaign slot; stale sites get skipped.

## Off-site (checklist for after launch — this is what actually moves AI rankings)
- [ ] **Google Business Profile** — claimed, categorized (Non-profit organization, Food bank), hours, photos, posts. LLMs lean heavily on this for "near me" queries.
- [ ] **Consistent NAP** (name/address/phone) across: 211 / United Way directory, FindHelp.org (aka Aunt Bertha), FoodPantries.org, Charity Navigator, GuideStar/Candid (claim the profile, earn a transparency seal), GreatNonprofits, Bing Places, Apple Maps.
- [ ] **Wikipedia-adjacent citations** — local news coverage (Times Union, Troy Record) of the 40th anniversary, garden, and mural; LLMs weight edited/news sources heavily.
- [ ] **Freefoodfridge listing** — if Troy has a fridge network map (freefoodfridgealbany etc.), get listed. ⚑
- [ ] Encourage partners (Capital Roots, Mohawk Global, congregations) to link to specific program pages, not just the homepage.
