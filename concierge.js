/* TAUM concierge: a lightweight, in-browser help assistant.
   No network calls, no third parties. Everything a visitor types
   stays on their own device. */
(function () {
  var KB = [
    {
      id: 'food',
      keys: ['food','eat','eating','hungry','hunger','meal','meals','lunch','dinner','breakfast','pantry','grocery','groceries','fridge','refrigerator','feed','starving','free food'],
      answer: "Free food, no questions asked. Hot lunch is served <strong>weekdays 11:30 am to 1:00 pm</strong> at 392 Second Street. Groceries are available during office hours, and the <strong>Free Food Fridge outside our building is open 24 hours a day</strong>. You don't need an ID or a referral.",
      links: [['See the food page','food.html'],['Call (518) 274-5920','tel:+15182745920']]
    },
    {
      id: 'furniture-get',
      keys: ['furniture','bed','beds','bedroom','mattress','dresser','table','chair','chairs','couch','sofa','need furniture','get furniture','sleep on','nothing to sit'],
      answer: "TAUM's Furniture Program gives good, used furniture <strong>free of charge, with free delivery</strong>, to neighbors starting over. Call <strong>Tyara Burnett at (518) 274-5920 ext. 204</strong>. If you're working with a caseworker or shelter, they can refer you directly.",
      links: [['See the furniture page','furniture.html'],['Call ext. 204','tel:+15182745920']]
    },
    {
      id: 'furniture-give',
      keys: ['donate furniture','give furniture','pick up','pickup','haul','get rid of','drop off furniture'],
      answer: "Yes, and we come to you. We accept clean, good-condition <strong>beds, dressers, tables, chairs, and living room furniture</strong>, with free pickup from your home. We can't take sleeper sofas or anything needing repair. Call <strong>(518) 274-5920 ext. 204</strong>.",
      links: [['Furniture details','furniture.html'],['Request a pickup','contact.html']]
    },
    {
      id: 'volunteer',
      keys: ['volunteer','volunteering','help out','get involved','give time','serve','sign up'],
      answer: "We'd love the help. There's kitchen crew, furniture moving, stocking the Free Food Fridge, gardening, mentoring teens, and event help. Tell us what interests you and we'll be in touch within a week.",
      links: [['Get involved','get-involved.html'],['Volunteer form','https://forms.gle/BWSMYFMgri5MybR97']]
    },
    {
      id: 'donate',
      keys: ['donate','donation','donating','give money','giving','contribute','support you','gift','tax','deductible','write a check','help with money','send money','fundraise','sponsor'],
      answer: "Thank you. You can give online, by mail (checks to Troy Area United Ministries, 392 2nd Street, Troy, NY 12180), or give furniture and food. TAUM is a 501(c)(3), so gifts are tax-deductible.",
      links: [['Ways to give','donate.html']]
    },
    {
      id: 'teens',
      keys: ['teen','teens','youth','summer job','summer program','laptop','coding','computer class','tech for teens','student job','job training'],
      answer: "Tech for Teens is a <strong>free summer program</strong> for Rensselaer County teens. Students earn a paycheck through the county's Summer Youth Employment Program, learn Office, web design, and coding, and every graduate keeps a refurbished laptop. Applications open each spring.",
      links: [['Tech for Teens','tech-for-teens.html'],['Call ext. 202','tel:+15182745920']]
    },
    {
      id: 'scholarship',
      keys: ['scholarship','college','mlk','king','apply','tuition','financial aid'],
      answer: "The Rev. Martin Luther King Jr. Scholarship supports Rensselaer County graduates heading to college. Applications must be <strong>postmarked by May 4</strong> each year. Questions go to Rev. Abby Norton-Levering at nortonlevering@taum.org.",
      links: [['Scholarship details','mlk-scholarship.html']]
    },
    {
      id: 'damien',
      keys: ['damien','illness','sick','hiv','aids','cancer','support group','peer support','diagnosis'],
      answer: "The Damien Center is a hospitality center for neighbors living with serious illness: meals, friendship, peer support, and help finding services. Everyone is welcome at the table. Reach <strong>Barbara Healey at (518) 274-5920 ext. 206</strong>.",
      links: [['Damien Center','damien-center.html']]
    },
    {
      id: 'chaplain',
      keys: ['chaplain','chaplaincy','sage','campus','spiritual','pray','prayer','counsel','someone to talk'],
      answer: "Our chaplaincy at Russell Sage College offers pastoral care, de-stress meditation, and care packages for students and staff of every background, every belief, or none. Contact <strong>Darren Gundrum at 518-244-4522</strong> or gundrd@sage.edu.",
      links: [['Campus chaplaincy','chaplaincy.html']]
    },
    {
      id: 'building',
      keys: ['building','room','space','rent','host','meeting','event space','use the building','venue'],
      answer: "Our community room, kitchen, and meeting spaces at 392 Second Street are available for neighborhood groups, classes, celebrations, and community projects. Ask us and we'll walk you through it.",
      links: [['Ask about the building','get-involved.html#building'],['Contact us','contact.html']]
    },
    {
      id: 'hours',
      keys: ['hours','open','close','closed','when','today','time','what time','directions','address','where','location','parking','find you'],
      answer: "We're at <strong>392 2nd Street, Troy, NY 12180</strong>. Office hours are <strong>Monday to Friday, 9 am to 4 pm</strong>. Hot lunch is 11:30 to 1 on weekdays, and the Free Food Fridge outside never closes.",
      links: [['Contact and directions','contact.html'],['Call (518) 274-5920','tel:+15182745920']]
    },
    {
      id: 'religion',
      keys: ['religious','religion','church','faith','christian','have to believe','preach','ministries'],
      answer: "No requirement at all. TAUM was founded by Troy-area congregations in 1986, but our services are for everyone, every faith or none. Nobody will preach at you, and help never comes with strings.",
      links: [['About TAUM','about.html']]
    },
    {
      id: 'contact',
      keys: ['contact','phone','call','email','talk to someone','staff','who do i','speak'],
      answer: "The front desk can point you to the right person: <strong>(518) 274-5920</strong>, or lmalatesta@taum.org. Each program also has its own contact listed on the programs page.",
      links: [['All programs and contacts','programs.html'],['Contact page','contact.html']]
    }
  ];

  /* Who handles what. Each knowledge entry names a staff contact so the
     concierge can route a visitor to the right person. */
  var STAFF = {
    food:      { who: 'Barbara Healey', role: 'Damien Center Coordinator', ext: '206', email: 'lmalatesta@taum.org', subject: 'Question about food and meals' },
    furniture: { who: 'Tyara Burnett',  role: 'Furniture Program Director', ext: '204', email: 'lmalatesta@taum.org', subject: 'Question about the Furniture Program' },
    teens:     { who: 'the Tech for Teens team', role: 'Program office', ext: '202', email: 'nortonlevering@taum.org', subject: 'Question about Tech for Teens' },
    abby:      { who: 'Rev. Abby Norton-Levering', role: 'Executive Director', ext: '202', email: 'nortonlevering@taum.org', subject: 'Question for TAUM' },
    chaplain:  { who: 'Darren Gundrum', role: 'Chaplain, Russell Sage College', phone: '5182444522', phoneLabel: '518-244-4522', email: 'gundrd@sage.edu', subject: 'Question about campus chaplaincy' },
    office:    { who: 'Lynn Malatesta', role: 'Office Administrator', ext: '', email: 'lmalatesta@taum.org', subject: 'Question for TAUM' }
  };

  var ROUTE = {
    food: 'food', 'furniture-get': 'furniture', 'furniture-give': 'furniture',
    volunteer: 'office', donate: 'abby', teens: 'teens', scholarship: 'abby',
    damien: 'food', chaplain: 'chaplain', building: 'abby', hours: 'office',
    religion: 'office', contact: 'office'
  };

  var URGENT = ['emergency','suicide','suicidal','kill myself','hurt myself','abuse','abusive','being hit','hits me','danger','dangerous','crisis','overdose','911',
                'unsafe','not safe','afraid of him','afraid of her','domestic violence','threatening me','scared to go home','nowhere to sleep','homeless tonight'];

  var GREET = ['hi','hello','hey','good morning','good afternoon','thanks','thank you','ok','okay'];

  function normalize(s) { return (' ' + s.toLowerCase().replace(/[^a-z0-9 ]/g, ' ') + ' ').replace(/\s+/g, ' '); }

  function score(text, entry) {
    var t = normalize(text), s = 0;
    entry.keys.forEach(function (k) {
      if (t.indexOf(' ' + k + ' ') !== -1) s += k.indexOf(' ') !== -1 ? 3 : 2;
      else if (t.indexOf(k) !== -1) s += 1;
    });
    return s;
  }

  function answerFor(text) {
    var t = normalize(text);
    for (var i = 0; i < URGENT.length; i++) {
      if (t.indexOf(URGENT[i]) !== -1) {
        return {
          id: 'urgent',
          answer: "If you're in immediate danger or a crisis, please call <strong>911</strong>, or the <strong>988 Suicide and Crisis Lifeline</strong> (call or text 988). For domestic violence, the Samaritans hotline in Troy is <strong>518-689-4673</strong>. TAUM's office is at (518) 274-5920 during weekday hours, and we'll help however we can.",
          links: [['Call 988', 'tel:988'], ['Call TAUM', 'tel:+15182745920']]
        };
      }
    }
    var best = null, bestScore = 0;
    KB.forEach(function (e) { var s = score(text, e); if (s > bestScore) { bestScore = s; best = e; } });
    if (best && bestScore >= 2) return best;
    if (GREET.indexOf(t.trim()) !== -1) {
      return { id: 'greet', answer: "Hello. Ask me about food, furniture, volunteering, donating, our programs, or our hours.", links: [] };
    }
    return {
      id: 'contact',
      answer: "I'm not sure about that one, and I'd rather connect you with a person than guess.",
      links: [['See all programs','programs.html']]
    };
  }

  var CHIPS = [
    ['I need food', 'I need food'],
    ['I need furniture', 'I need furniture'],
    ['I want to volunteer', 'I want to volunteer'],
    ['Hours and directions', 'What are your hours and where are you?'],
    ['How do I donate?', 'How do I donate?']
  ];

  var css = ''
    + '.tc-btn{position:fixed;right:18px;bottom:18px;z-index:900;display:flex;align-items:center;gap:9px;'
    + 'background:var(--coral,#F0765C);color:var(--ink,#20264C);border:2px solid var(--ink,#20264C);'
    + 'border-radius:999px;padding:12px 20px;font-family:inherit;font-weight:900;font-size:1rem;cursor:pointer;'
    + 'box-shadow:4px 4px 0 var(--ink,#20264C);transition:transform .15s,box-shadow .15s}'
    + '.tc-btn:hover{transform:translate(-2px,-2px);box-shadow:6px 6px 0 var(--ink,#20264C)}'
    + '.tc-btn svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round}'
    + '.tc-panel{position:fixed;right:18px;bottom:18px;z-index:901;width:370px;max-width:calc(100vw - 24px);'
    + 'max-height:min(620px,calc(100vh - 36px));display:none;flex-direction:column;overflow:hidden;'
    + 'background:var(--cream,#FBF7EC);border:2px solid var(--ink,#20264C);border-radius:16px;box-shadow:6px 6px 0 var(--ink,#20264C)}'
    + '.tc-panel.open{display:flex}'
    + '.tc-head{background:var(--indigo,#2A3060);color:var(--cream,#FBF7EC);padding:14px 16px;display:flex;align-items:center;gap:10px;flex-shrink:0}'
    + '.tc-head b{font-family:Fraunces,Georgia,serif;font-size:1.05rem;font-weight:700}'
    + '.tc-head span{font-size:.78rem;opacity:.85;display:block;font-weight:600}'
    + '.tc-x{margin-left:auto;background:none;border:none;color:var(--cream,#FBF7EC);font-size:1.5rem;line-height:1;cursor:pointer;padding:0 4px}'
    + '.tc-log{padding:16px;overflow-y:auto;flex:1;display:flex;flex-direction:column;gap:12px}'
    + '.tc-msg{max-width:88%;padding:11px 14px;border-radius:14px;font-size:.94rem;line-height:1.55}'
    + '.tc-bot{background:#fff;border:2px solid var(--ink,#20264C);align-self:flex-start;color:var(--ink,#20264C)}'
    + '.tc-me{background:var(--aqua,#7AD4E8);border:2px solid var(--ink,#20264C);align-self:flex-end;color:var(--ink,#20264C);font-weight:700}'
    + '.tc-links{display:flex;flex-wrap:wrap;gap:7px;margin-top:10px}'
    + '.tc-links a{background:var(--coral,#F0765C);color:var(--ink,#20264C);border:2px solid var(--ink,#20264C);'
    + 'border-radius:999px;padding:5px 12px;font-size:.82rem;font-weight:800;text-decoration:none}'
    + '.tc-chips{display:flex;flex-wrap:wrap;gap:7px;padding:0 16px 12px;flex-shrink:0}'
    + '.tc-chips button{background:#fff;border:2px solid var(--ink,#20264C);border-radius:999px;padding:6px 13px;'
    + 'font-family:inherit;font-size:.83rem;font-weight:800;color:var(--ink,#20264C);cursor:pointer}'
    + '.tc-chips button:hover{background:var(--aqua-pale,#DCF2F8)}'
    + '.tc-form{display:flex;gap:8px;padding:12px 16px;border-top:2px solid var(--ink,#20264C);background:#fff;flex-shrink:0}'
    + '.tc-form input{flex:1;min-width:0;padding:10px 14px;border:2px solid var(--ink,#20264C);border-radius:999px;font-family:inherit;font-size:.95rem}'
    + '.tc-form button{background:var(--indigo,#2A3060);color:var(--cream,#FBF7EC);border:2px solid var(--ink,#20264C);'
    + 'border-radius:999px;padding:10px 16px;font-family:inherit;font-weight:900;cursor:pointer}'
    + '.tc-icon{background:#fff !important;color:var(--ink,#20264C) !important;padding:10px 12px !important;display:flex;align-items:center;justify-content:center}'
    + '.tc-icon svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:2.1;stroke-linecap:round;stroke-linejoin:round}'
    + '.tc-icon.on{background:var(--coral,#F0765C) !important}'
    + '.tc-icon.rec{background:var(--coral,#F0765C) !important;animation:tcPulse 1.1s ease-in-out infinite}'
    + '@keyframes tcPulse{0%,100%{transform:scale(1)}50%{transform:scale(1.12)}}'
    + '@media (prefers-reduced-motion: reduce){.tc-icon.rec{animation:none}}'
    + '.tc-voicebar{display:flex;align-items:center;gap:8px;padding:0 16px 10px;font-size:.78rem;color:var(--indigo-soft,#4A5185);flex-shrink:0}'
    + '.tc-note{font-size:.72rem;color:var(--indigo-soft,#4A5185);text-align:center;padding:0 16px 10px;flex-shrink:0}'
    + '@media(max-width:480px){.tc-panel{right:8px;left:8px;bottom:8px;width:auto;max-height:calc(100vh - 16px)}'
    + '.tc-btn{right:12px;bottom:12px;padding:11px 17px;font-size:.94rem}}';

  var style = document.createElement('style');
  style.textContent = css;
  document.head.appendChild(style);

  var btn = document.createElement('button');
  btn.className = 'tc-btn';
  btn.setAttribute('aria-label', 'Open the TAUM help assistant');
  btn.innerHTML = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12a8 8 0 1 1-3.2-6.4"/><path d="M8 11h8M8 15h5"/></svg> How can we help?';

  var panel = document.createElement('div');
  panel.className = 'tc-panel';
  panel.setAttribute('role', 'dialog');
  panel.setAttribute('aria-label', 'TAUM help assistant');
  panel.innerHTML =
      '<div class="tc-head"><div><b>TAUM Help Desk</b><span>Quick answers, any time</span></div>'
    + '<button class="tc-x" aria-label="Close">&times;</button></div>'
    + '<div class="tc-log" id="tcLog" aria-live="polite"></div>'
    + '<div class="tc-chips" id="tcChips"></div>'
    + '<form class="tc-form" id="tcForm">'
    + '<button type="button" class="tc-icon" id="tcMic" aria-label="Speak your question" title="Speak your question">'
    + '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="9" y="3" width="6" height="11" rx="3"/><path d="M5 11a7 7 0 0 0 14 0M12 18v3"/></svg></button>'
    + '<button type="button" class="tc-icon" id="tcSpk" aria-label="Read answers aloud" aria-pressed="false" title="Read answers aloud">'
    + '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 9v6h4l5 4V5L8 9H4z"/><path d="M17 8.5a5 5 0 0 1 0 7"/></svg></button>'
    + '<input id="tcInput" type="text" autocomplete="off" '
    + 'placeholder="Ask about food, furniture, hours..." aria-label="Type your question"><button type="submit">Ask</button></form>'
    + '<p class="tc-voicebar" id="tcVoiceBar"></p>'
    + '<p class="tc-note">Answers come from this website. Nothing you type or say leaves your device.</p>';

  document.body.appendChild(btn);
  document.body.appendChild(panel);

  var log = panel.querySelector('#tcLog');
  var chips = panel.querySelector('#tcChips');
  var form = panel.querySelector('#tcForm');
  var input = panel.querySelector('#tcInput');

  function add(html, who, links) {
    var d = document.createElement('div');
    d.className = 'tc-msg ' + (who === 'me' ? 'tc-me' : 'tc-bot');
    d.innerHTML = html;
    if (links && links.length) {
      var wrap = document.createElement('div');
      wrap.className = 'tc-links';
      links.forEach(function (l) {
        var a = document.createElement('a');
        a.href = l[1];
        a.textContent = l[0];
        wrap.appendChild(a);
      });
      d.appendChild(wrap);
    }
    log.appendChild(d);
    log.scrollTop = log.scrollHeight;
    if (who !== 'me' && typeof say === 'function') say(html);
  }

  var lastQuestion = '';

  function mailtoFor(staffKey, question) {
    var s = STAFF[staffKey] || STAFF.office;
    var body = "Hello " + s.who.replace('the ', '') + ",\n\n"
      + (question ? "I asked this on the TAUM website:\n\"" + question + "\"\n\n" : "")
      + "Could you help me with this?\n\nThank you,\n";
    return 'mailto:' + s.email
      + '?subject=' + encodeURIComponent(s.subject + ' (from the website)')
      + '&body=' + encodeURIComponent(body);
  }

  function handoff(staffKey, question) {
    var s = STAFF[staffKey] || STAFF.office;
    var phoneHref = s.phone ? 'tel:+1' + s.phone : 'tel:+15182745920';
    var phoneText = s.phoneLabel ? s.phoneLabel : ('(518) 274-5920' + (s.ext ? ' ext. ' + s.ext : ''));
    var msg = "Want to talk to a person about this? <strong>" + s.who + "</strong>"
      + (s.role ? ', ' + s.role : '') + " handles this one.";
    var links = [
      ['Call ' + phoneText, phoneHref],
      ['Email ' + s.who.split(' ')[0], mailtoFor(staffKey, question)]
    ];
    add(msg, 'bot', links);
  }

  function ask(text) {
    lastQuestion = text;
    add(text.replace(/</g, '&lt;'), 'me');
    var r = answerFor(text);
    setTimeout(function () {
      add(r.answer, 'bot', r.links);
      var key = ROUTE[r.id];
      if (key) setTimeout(function () { handoff(key, text); }, 400);
    }, 260);
  }

  CHIPS.forEach(function (c) {
    var b = document.createElement('button');
    b.type = 'button';
    b.textContent = c[0];
    b.addEventListener('click', function () { ask(c[1]); });
    chips.appendChild(b);
  });

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var v = input.value.trim();
    if (!v) return;
    input.value = '';
    ask(v);
  });

  /* ---- Voice: speak your question, and hear answers read aloud ----
     Uses the browser's own speech engines. No service, no account,
     and nothing spoken is sent anywhere. */
  var mic = panel.querySelector('#tcMic');
  var spk = panel.querySelector('#tcSpk');
  var voiceBar = panel.querySelector('#tcVoiceBar');
  var SR = window.SpeechRecognition || window.webkitSpeechRecognition;
  var canHear = !!SR;
  var canSpeak = 'speechSynthesis' in window;
  var speakOn = false;
  var rec = null, listening = false;

  function say(html) {
    if (!canSpeak || !speakOn) return;
    var tmp = document.createElement('div');
    tmp.innerHTML = html;
    var text = (tmp.textContent || '').replace(/\s+/g, ' ').trim();
    if (!text) return;
    try {
      window.speechSynthesis.cancel();
      var u = new SpeechSynthesisUtterance(text);
      u.rate = 0.98;
      u.pitch = 1;
      window.speechSynthesis.speak(u);
    } catch (e) { /* speech unavailable; text is still on screen */ }
  }

  if (!canHear) {
    mic.style.display = 'none';
  } else {
    mic.addEventListener('click', function () {
      if (listening) { try { rec.stop(); } catch (e) {} return; }
      try {
        rec = new SR();
        rec.lang = 'en-US';
        rec.interimResults = false;
        rec.maxAlternatives = 1;
        rec.onstart = function () {
          listening = true;
          mic.classList.add('rec');
          voiceBar.textContent = 'Listening. Speak your question.';
        };
        rec.onresult = function (e) {
          var said = e.results[0][0].transcript;
          voiceBar.textContent = '';
          if (said) ask(said);
        };
        rec.onerror = function (e) {
          voiceBar.textContent = e.error === 'not-allowed'
            ? 'Microphone blocked. You can allow it in your browser settings, or type instead.'
            : 'Sorry, I did not catch that. Try again or type your question.';
        };
        rec.onend = function () { listening = false; mic.classList.remove('rec'); };
        rec.start();
      } catch (err) {
        voiceBar.textContent = 'Voice input is not available in this browser. Please type instead.';
      }
    });
  }

  if (!canSpeak) {
    spk.style.display = 'none';
  } else {
    spk.addEventListener('click', function () {
      speakOn = !speakOn;
      spk.classList.toggle('on', speakOn);
      spk.setAttribute('aria-pressed', speakOn ? 'true' : 'false');
      if (speakOn) {
        voiceBar.textContent = 'Reading answers aloud.';
        var last = log.querySelector('.tc-bot:last-of-type');
        if (last) say(last.innerHTML);
      } else {
        voiceBar.textContent = '';
        try { window.speechSynthesis.cancel(); } catch (e) {}
      }
    });
  }

  var started = false;
  function open() {
    panel.classList.add('open');
    btn.style.display = 'none';
    if (!started) {
      started = true;
      add("Hi. I can help you find food, furniture, volunteer info, our hours, and more. What do you need?", 'bot');
    }
    setTimeout(function () { input.focus(); }, 60);
  }
  function close() {
    panel.classList.remove('open');
    btn.style.display = '';
    btn.focus();
    try { if (window.speechSynthesis) window.speechSynthesis.cancel(); } catch (e) {}
    try { if (listening && rec) rec.stop(); } catch (e) {}
  }

  btn.addEventListener('click', open);
  panel.querySelector('.tc-x').addEventListener('click', close);
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && panel.classList.contains('open')) close();
  });
})();
