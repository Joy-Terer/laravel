<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SmartChama — Kenya's Digital Savings Platform</title>
  <meta name="description" content="Manage your chama with confidence and clarity. Track contributions, manage loans, and grow together."/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --navy:    #0f172a;
      --blue:    #1d4ed8;
      --blue-d:  #1e40af;
      --blue-l:  #eff6ff;
      --blue-b:  #bfdbfe;
      --accent:  #00c897;
      --gold:    #f59e0b;
      --ink:     #0f172a;
      --soft:    #64748b;
      --muted:   #94a3b8;
      --border:  #e2e8f0;
      --bg:      #f8fafc;
      --white:   #ffffff;
      --serif:   'Playfair Display', Georgia, serif;
      --sans:    'DM Sans', sans-serif;
    }
    html { scroll-behavior: smooth; }
    body { font-family: var(--sans); color: var(--ink); background: var(--white); overflow-x: hidden; line-height: 1.6; }

    /* ── NAV ──────────────────────────────────────────────────── */
    nav {
      display: flex; align-items: center; justify-content: space-between;
      padding: 16px 5%;
      position: sticky; top: 0; z-index: 200;
      background: rgba(255,255,255,.96);
      backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--border);
      transition: box-shadow .3s;
    }
    nav.scrolled { box-shadow: 0 2px 20px rgba(0,0,0,.07); }
    .logo { font-family: var(--serif); font-size: 22px; font-weight: 700; color: var(--navy); text-decoration: none; letter-spacing: -.3px; }
     .logo span { color: var(--blue); }
    nav ul { display: flex; gap: 28px; list-style: none; }
    nav ul a { text-decoration: none; font-size: 14px; color: var(--soft); font-weight: 500; transition: color .2s; }
    nav ul a:hover { color: var(--navy); }
    .nav-cta { display: flex; gap: 10px; align-items: center; }
    .btn-nav-ghost { font-size: 14px; font-weight: 500; color: var(--navy); text-decoration: none; padding: 8px 16px; border-radius: 6px; transition: background .2s; }
    .btn-nav-ghost:hover { background: var(--blue-l); color: var(--blue); }
    .btn-nav-fill { background: var(--blue); color: white; padding: 8px 18px; border-radius: 6px; font-size: 14px; font-weight: 600; text-decoration: none; font-family: var(--sans); transition: background .2s; display: inline-block; }
    .btn-nav-fill:hover { background: var(--blue-d); }
    .hamburger { display: none; flex-direction: column; gap: 5px; cursor: pointer; background: none; border: none; padding: 4px; }
    .hamburger span { display: block; width: 22px; height: 2px; background: var(--ink); border-radius: 2px; }

    /* ── HERO ─────────────────────────────────────────────────── */
    .hero { display: grid; grid-template-columns: 1fr 1fr; min-height: 92vh; align-items: center; overflow: hidden; }
    .hero-text { padding: 60px 5% 60px 7%; }
    .hero-badge {
      display: inline-flex; align-items: center; gap: 6px;
      background: var(--blue-l); color: var(--blue);
      font-size: 12px; font-weight: 600; padding: 5px 14px;
      border-radius: 20px; margin-bottom: 22px; border: 1px solid var(--blue-b);
    }
   .hero-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 0;
    }

    .hero-badge-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--accent); }
    .hero h1 { font-family: var(--serif); font-size: clamp(36px, 4.5vw, 60px); line-height: 1.12; color: var(--ink); margin-bottom: 20px; letter-spacing: -.5px; }
    .hero h1 em { color: var(--blue); font-style: normal; position: relative; display: inline-block; }
    .hero h1 em::after { content: ''; position: absolute; bottom: 2px; left: 0; right: 0; height: 3px; background: var(--accent); border-radius: 2px; }
    .hero p { font-size: 16px; color: var(--soft); line-height: 1.75; margin-bottom: 32px; max-width: 440px; }
    .btn-row { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 44px; }
    .btn-primary { background: var(--blue); color: white; padding: 13px 26px; border-radius: 8px; font-size: 15px; font-weight: 600; text-decoration: none; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-family: var(--sans); transition: background .2s, transform .2s; }
    .btn-primary:hover { background: var(--blue-d); transform: translateY(-1px); }
    .btn-secondary { background: transparent; color: var(--blue); padding: 13px 26px; border-radius: 8px; font-size: 15px; font-weight: 600; text-decoration: none; border: 2px solid var(--blue-b); cursor: pointer; display: inline-flex; align-items: center; gap: 6px; font-family: var(--sans); transition: background .2s, border-color .2s; }
    .btn-secondary:hover { background: var(--blue-l); border-color: var(--blue); }
    .hero-stats { display: flex; gap: 0; padding-top: 28px; border-top: 1px solid var(--border); flex-wrap: wrap; }
    .hero-stat { padding-right: 28px; margin-right: 28px; border-right: 1px solid var(--border); }
    .hero-stat:last-child { border-right: none; }
    .hero-stat-num { font-family: var(--serif); font-size: 26px; font-weight: 700; color: var(--ink); }
    .hero-stat-lbl { font-size: 12px; color: var(--muted); margin-top: 2px; }

    /* Hero visual  */
    .hero-visual{
      background: linear-gradient(145deg, var(--navy) 0%, #162044 100%);
      min-height: 92vh; 
      display: flex; 
      align-items: center; 
      justify-content: center; 
      padding: 40px;
      position: relative; 
      overflow: hidden;
    }
    .hero-visual img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
}

    .hero-visual::before {
      content: ''; 
      position: absolute; 
      inset: 0;
      background:rgba(15,23,42,0.4);
    }
    .dash-card {
      background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.15);
      border-radius: 20px; padding: 28px; width: 100%; max-width: 340px;
      backdrop-filter: blur(16px); position: relative; z-index: 1;
    }
    .dash-card h3 { color: rgba(255,255,255,.85); font-size: 14px; font-weight: 500; margin-bottom: 20px; }
    .stat-row { display: flex; justify-content: space-between; margin-bottom: 22px; }
    .d-stat { text-align: center; }
    .d-stat .num { color: white; font-size: 22px; font-weight: 700; font-family: var(--serif); }
    .d-stat .lbl { color: rgba(255,255,255,.55); font-size: 11px; margin-top: 2px; }
    .progress-block { margin-bottom: 12px; }
    .progress-block .plabel { display: flex; justify-content: space-between; font-size: 12px; color: rgba(255,255,255,.65); margin-bottom: 6px; }
    .progress-bar { background: rgba(255,255,255,.15); border-radius: 4px; height: 7px; }
    .progress-fill { background: var(--accent); border-radius: 4px; height: 7px; }
    .mpesa-badge { display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,.08); border-radius: 8px; padding: 10px 14px; margin-top: 16px; border: 1px solid rgba(255,255,255,.1); }
    .mpesa-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--accent); box-shadow: 0 0 6px var(--accent); }
    .mpesa-text { color: rgba(255,255,255,.8); font-size: 12px; font-weight: 500; }

    /* ── LOGOS ────────────────────────────────────────────────── */
    .logos { background: var(--white); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); padding: 28px 5%; text-align: center; }
    .logos-label { font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 18px; }
    .logos-row { display: flex; align-items: center; justify-content: center; gap: 40px; flex-wrap: wrap; }
    .logo-item { display: flex; align-items: center; gap: 7px; font-size: 14px; font-weight: 700; color: #b0bec5; font-family: var(--serif); }
    .logo-item i { font-size: 18px; }

    /* ── SECTIONS ─────────────────────────────────────────────── */
    section { padding: 80px 5%; }
    .section-tag { font-size: 12px; font-weight: 600; color: var(--blue); letter-spacing: 1.5px; text-transform: uppercase; display: block; margin-bottom: 10px; }
    .section-title { font-family: var(--serif); font-size: clamp(26px, 3.5vw, 40px); color: var(--ink); margin-bottom: 14px; line-height: 1.15; }
    .section-sub { font-size: 16px; color: var(--soft); line-height: 1.7; max-width: 520px; }
    .reveal { opacity: 0; transform: translateY(24px); transition: opacity .55s, transform .55s; }
    .reveal.visible { opacity: 1; transform: none; }
    .d1 { transition-delay: .1s; } .d2 { transition-delay: .2s; } .d3 { transition-delay: .3s; }

    /* ── SLIDER ───────────────────────────────────────────────── */
    .slider-section { background: var(--bg); border-top: 1px solid var(--border); padding: 0; }
    .slides-wrap { overflow: hidden; }
    .slides { display: flex; transition: transform .5s ease; }
    .slide { min-width: 100%; padding: 56px 7%; display: flex; align-items: center; gap: 48px; }
    .slide-icon { width: 68px; height: 68px; border-radius: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 28px; }
    .slide-icon.blue { background: var(--blue-l); color: var(--blue); }
    .slide-icon.gold { background: #fffbeb; color: var(--gold); }
    .slide-icon.teal { background: #ecfeff; color: #0891b2; }
    .slide-text h2 { font-family: var(--serif); font-size: 26px; margin-bottom: 10px; color: var(--ink); }
    .slide-text p { color: var(--soft); font-size: 15px; line-height: 1.75; max-width: 560px; }
    .slide-dots { display: flex; justify-content: center; gap: 8px; padding: 20px 0; background: var(--bg); }
    .dot { width: 8px; height: 8px; border-radius: 50%; background: #cbd5e1; cursor: pointer; border: none; transition: all .3s; }
    .dot.active { background: var(--blue); width: 24px; border-radius: 4px; }

    /* ── MISSION ──────────────────────────────────────────────── */
    .mission { text-align: center; border-top: 1px solid var(--border); }
    .mission .section-title { max-width: 600px; margin: 0 auto 14px; }
    .mission .section-sub { margin: 0 auto 44px; }
    .mission-cards { display: grid; grid-template-columns: repeat(3,1fr); gap: 20px; max-width: 900px; margin: 0 auto; }
    .mcard { background: var(--bg); border-radius: 14px; padding: 28px; text-align: left; border: 1px solid var(--border); transition: border-color .2s, transform .2s; }
    .mcard:hover { border-color: var(--blue); transform: translateY(-3px); }
    .mcard .icon { font-size: 24px; margin-bottom: 14px; }
    .mcard h4 { font-size: 15px; font-weight: 600; margin-bottom: 8px; color: var(--ink); }
    .mcard p { font-size: 14px; color: var(--soft); line-height: 1.65; }

    /* ── SERVICES ─────────────────────────────────────────────── */
    .services { background: var(--bg); border-top: 1px solid var(--border); }
    .services-header { text-align: center; margin-bottom: 48px; }
    .services-header .section-sub { margin: 0 auto; }
    .services-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; max-width: 960px; margin: 0 auto; }
    .scard { background: var(--white); border-radius: 14px; padding: 26px; border: 1px solid var(--border); transition: border-color .2s, transform .2s; }
    .scard:hover { border-color: var(--blue); transform: translateY(-3px); }
    .scard .s-icon { width: 44px; height: 44px; border-radius: 10px; background: var(--blue-l); display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 16px; }
    .scard h4 { font-size: 15px; font-weight: 600; margin-bottom: 8px; color: var(--ink); }
    .scard p { font-size: 13px; color: var(--soft); line-height: 1.65; }

    /* ── WHY ──────────────────────────────────────────────────── */
    .why { border-top: 1px solid var(--border); }
    .why-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center; max-width: 960px; margin: 0 auto; }
    .why-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    .why-item { display: flex; align-items: flex-start; gap: 12px; }
    .why-check { width: 28px; height: 28px; border-radius: 50%; background: var(--blue-l); display: flex; align-items: center; justify-content: center; flex-shrink: 0; color: var(--blue); font-size: 13px; font-weight: 700; }
    .why-item h5 { font-size: 14px; font-weight: 600; margin-bottom: 3px; color: var(--ink); }
    .why-item p { font-size: 13px; color: var(--soft); line-height: 1.5; }

    /* ── PRICING ──────────────────────────────────────────────── */
    .pricing { background: var(--bg); border-top: 1px solid var(--border); text-align: center; }
    .pricing .section-sub { margin: 0 auto 48px; }
    .pricing-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 18px; max-width: 1100px; margin: 0 auto; }
    .price-card { background: var(--white); border: 1.5px solid var(--border); border-radius: 16px; padding: 28px 22px; display: flex; flex-direction: column; position: relative; text-align: left; transition: transform .2s, box-shadow .2s; }
    .price-card:hover { transform: translateY(-4px); box-shadow: 0 12px 36px rgba(0,0,0,.07); }
    .price-card-featured { border-color: var(--blue); }
    .price-card-featured::before { content: 'Most Popular'; position: absolute; top: -13px; left: 50%; transform: translateX(-50%); background: var(--blue); color: white; font-size: 11px; font-weight: 700; padding: 3px 14px; border-radius: 20px; white-space: nowrap; }
    .price-name { font-family: var(--serif); font-size: 18px; font-weight: 700; margin-bottom: 10px; color: var(--ink); }
    .price-amount { font-family: var(--serif); font-size: 32px; font-weight: 700; color: var(--blue); margin-bottom: 2px; }
    .price-period { font-size: 13px; color: var(--muted); margin-bottom: 22px; }
    .price-features { list-style: none; flex: 1; margin-bottom: 22px; display: flex; flex-direction: column; gap: 9px; }
    .price-features li { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--soft); }
    .price-features li.on { color: var(--ink); }
    .price-features li.on i { color: var(--accent); }
    .price-features li.off { opacity: .4; text-decoration: line-through; }
    .price-features li.off i { color: var(--muted); }
    .btn-price { width: 100%; padding: 11px; border-radius: 8px; font-size: 14px; font-weight: 700; font-family: var(--sans); cursor: pointer; text-align: center; text-decoration: none; display: block; border: 2px solid var(--border); background: white; color: var(--ink); transition: all .2s; }
    .btn-price:hover { border-color: var(--blue); background: var(--blue-l); color: var(--blue); }
    .btn-price-blue { background: var(--blue); color: white; border-color: var(--blue); }
    .btn-price-blue:hover { background: var(--blue-d); border-color: var(--blue-d); color: white; }

    /* ── TESTIMONIALS ─────────────────────────────────────────── */
    .testimonials { border-top: 1px solid var(--border); }
    .testimonials .section-title { text-align: center; margin-bottom: 40px; }
    .t-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; max-width: 960px; margin: 0 auto; }
    .tcard { background: var(--bg); border-radius: 14px; padding: 24px; border: 1px solid var(--border); }
    .tcard p { font-size: 14px; color: var(--soft); line-height: 1.75; margin-bottom: 18px; font-style: italic; }
    .t-author { display: flex; align-items: center; gap: 10px; }
    .t-avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--blue); display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 700; flex-shrink: 0; }
    .t-name { font-size: 13px; font-weight: 600; color: var(--ink); }
    .t-group { font-size: 12px; color: var(--muted); }

    /* ── WHO WE SERVE ─────────────────────────────────────────── */
    .clients { background: var(--bg); border-top: 1px solid var(--border); text-align: center; padding: 56px 5%; }
    .clients h3 { font-size: 13px; color: var(--muted); margin-bottom: 22px; letter-spacing: 1px; text-transform: uppercase; }
    .chips { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; }
    .chip { background: var(--blue-l); color: var(--blue); border-radius: 20px; padding: 7px 18px; font-size: 13px; font-weight: 600; border: 1px solid var(--blue-b); }

    /* ── CTA ──────────────────────────────────────────────────── */
    .cta-section { background: linear-gradient(145deg, var(--navy) 0%, #162044 100%); padding: 80px 5%; text-align: center; }
    .cta-section h2 { font-family: var(--serif); font-size: clamp(28px, 4vw, 42px); color: white; margin-bottom: 14px; }
    .cta-section p { color: rgba(255,255,255,.65); font-size: 16px; margin-bottom: 36px; }
    .cta-buttons { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
    .btn-white { background: white; color: var(--blue); padding: 14px 32px; border-radius: 8px; font-size: 15px; font-weight: 700; border: none; cursor: pointer; text-decoration: none; font-family: var(--sans); display: inline-flex; align-items: center; gap: 6px; transition: background .2s; }
    .btn-white:hover { background: var(--blue-l); }
    .btn-ghost-white { background: transparent; color: white; padding: 14px 32px; border-radius: 8px; font-size: 15px; font-weight: 600; border: 2px solid rgba(255,255,255,.3); cursor: pointer; text-decoration: none; font-family: var(--sans); display: inline-flex; align-items: center; gap: 6px; transition: border-color .2s, background .2s; }
    .btn-ghost-white:hover { border-color: rgba(255,255,255,.6); background: rgba(255,255,255,.08); }
    .cta-note { font-size: 12px; color: rgba(255,255,255,.4); margin-top: 16px; }

    /* ── FOOTER ───────────────────────────────────────────────── */
    footer { padding: 48px 5% 28px; border-top: 1px solid var(--border); display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 40px; }
    .footer-logo { font-family: var(--serif); font-size: 20px; font-weight: 700; color: var(--navy); margin-bottom: 10px; }
    .footer-logo span { color: var(--blue); }
    .footer-tagline { font-size: 13px; color: var(--soft); line-height: 1.65; max-width: 240px; margin-bottom: 12px; }
    .footer-contact { font-size: 13px; color: var(--soft); line-height: 1.9; }
    .footer-col-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .8px; color: var(--ink); margin-bottom: 14px; }
    .footer-links { list-style: none; display: flex; flex-direction: column; gap: 9px; }
    .footer-links a { font-size: 14px; color: var(--soft); text-decoration: none; transition: color .2s; }
    .footer-links a:hover { color: var(--blue); }
    .footer-bottom { padding: 20px 5%; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
    .footer-bottom p { font-size: 13px; color: var(--muted); }

    /* ── MOBILE NAV ───────────────────────────────────────────── */
    .mobile-nav { display: none; position: fixed; inset: 0; z-index: 300; background: white; padding: 80px 5% 40px; flex-direction: column; gap: 20px; overflow-y: auto; }
    .mobile-nav.open { display: flex; }
    .mobile-nav a { font-family: var(--serif); font-size: 24px; font-weight: 700; color: var(--ink); text-decoration: none; border-bottom: 1px solid var(--border); padding-bottom: 16px; }
    .mobile-nav-close { position: absolute; top: 20px; right: 5%; background: none; border: none; font-size: 24px; cursor: pointer; color: var(--ink); }

    /* ── ANIMATIONS ───────────────────────────────────────────── */
    @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
    .anim { opacity: 0; transform: translateY(28px); animation: fadeUp .65s forwards; }
    .a1 { animation-delay: .15s; } .a2 { animation-delay: .3s; } .a3 { animation-delay: .45s; } .a4 { animation-delay: .6s; }

    /* ── RESPONSIVE ───────────────────────────────────────────── */
    @media (max-width: 1100px) { .pricing-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 900px) {
      .hero { grid-template-columns: 1fr; }
      .hero-visual { min-height: 380px; }
      .mission-cards, .services-grid, .t-grid { grid-template-columns: 1fr 1fr; }
      .why-inner { grid-template-columns: 1fr; gap: 36px; }
      .slide { flex-direction: column; padding: 36px 5%; gap: 20px; }
      footer { grid-template-columns: 1fr 1fr; gap: 28px; }
      nav ul, .nav-cta { display: none; }
      .hamburger { display: flex; }
    }
    @media (max-width: 600px) {
      .hero-text { padding: 48px 5% 36px; }
      .hero h1 { font-size: 34px; }
      .mission-cards, .services-grid, .t-grid, .why-grid, .pricing-grid { grid-template-columns: 1fr; }
      .hero-stats { gap: 0; }
      footer { grid-template-columns: 1fr; }
      section { padding: 56px 5%; }
    }
  </style>
</head>
<body>

<!-- NAV -->
<nav id="navbar">
  <a href="#" class="logo">Smart<span>Chama</span></a>
  <ul>
    <li><a href="#services">Services</a></li>
    <li><a href="#why">Why Us</a></li>
    <li><a href="#pricing">Pricing</a></li>
    <li><a href="#testimonials">Reviews</a></li>
  </ul>
  <div class="nav-cta">
    <a href="/login" class="btn-nav-ghost">Sign in</a>
    <a href="/create-chama" class="btn-nav-fill">Get started free</a>
  </div>
  <button class="hamburger" onclick="toggleNav()" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- MOBILE NAV -->
<div class="mobile-nav" id="mobileNav">
  <button class="mobile-nav-close" onclick="toggleNav()"><i class="bi bi-x-lg"></i></button>
  <a href="#services" onclick="toggleNav()">Services</a>
  <a href="#why" onclick="toggleNav()">Why Us</a>
  <a href="#pricing" onclick="toggleNav()">Pricing</a>
  <a href="#testimonials" onclick="toggleNav()">Reviews</a>
  <a href="/login" onclick="toggleNav()">Sign in</a>
  <a href="/create-chama" onclick="toggleNav()" style="color:var(--blue)">Create your chama →</a>
</div>

<!-- HERO -->
<section class="hero">
  <div class="hero-text">
    <span class="hero-badge anim a1">
      <span class="hero-badge-dot"></span>
      Kenya's Smart Chama Platform
    </span>
    <h1 class="anim a2">Manage Your Chama with Confidence and have smooth operations</h1>
    <p class="anim a3">Welcome to SmartChama — your trusted partner in funding, contributions and group financial management. Transparent, accountable and built for communities.</p>
    <div class="btn-row anim a3">
      <a href="/create-chama" class="btn-primary"><i class="bi bi-rocket-takeoff"></i> Start saving today</a>
      <a href="#services" class="btn-secondary"><i class="bi bi-play-circle"></i> Learn more</a>
    </div>
    <div class="hero-stats anim a4">
      <div class="hero-stat">
        <div class="hero-stat-num">Real-time</div>
        <div class="hero-stat-lbl">Tracking</div>
      </div>

      <div class="hero-stat">
       <div class="hero-stat-num">Secure</div>
       <div class="hero-stat-lbl">Transactions</div>
      </div>

      <div class="hero-stat">
       <div class="hero-stat-num">Flexible</div>
       <div class="hero-stat-lbl">Groups</div>
      </div>
    </div>
  </div>
  <div class="hero-visual">
    <img src="{{ asset('/images/chama.png') }}" alt="chama">
  </div>
</section>

<!-- SLIDER -->
<section class="slider-section" id="services">
  <div class="slides-wrap">
    <div class="slides" id="slides">

      <div class="slide">
        <div class="slide-icon blue"><i class="bi bi-cash-stack"></i></div>
        <div class="slide-text">
          <h2>Contribution Management</h2>
          <p>
            Keep track of every member’s contributions without relying on notebooks or manual records. 
            Payments are recorded instantly, reminders help members stay consistent and you can always 
            see who has paid and who hasn’t.
          </p>
        </div>
      </div>

      <div class="slide">
        <div class="slide-icon gold"><i class="bi bi-arrow-repeat"></i></div>
        <div class="slide-text">
          <h2>Merry-Go-Round System</h2>
          <p>
            Manage rotating payouts with clear schedules so every member knows their turn. 
            Disbursements are tracked and all records remain visible to the group to avoid confusion 
            or disputes.
          </p>
        </div>
      </div>

      <div class="slide">
        <div class="slide-icon teal"><i class="bi bi-people-fill"></i></div>
        <div class="slide-text">
          <h2>Social Welfare Management</h2>
          <p>
            Organize welfare contributions when members need support. Record contributions, approve 
            requests and keep a clear history so everyone understands how funds are used.
          </p>
        </div>
      </div>

    </div>
  </div>

  <div class="slide-dots">
    <button class="dot active" onclick="goSlide(0)"></button>
    <button class="dot" onclick="goSlide(1)"></button>
    <button class="dot" onclick="goSlide(2)"></button>
  </div>
</section>

<!-- MISSION -->
<section class="mission reveal">
  <span class="section-tag">Our mission</span>
  <h2 class="section-title">Empowering Kenyan Savings Groups</h2>
  <p class="section-sub"></p>
  <div class="mission-cards">
    <div class="mcard reveal d1">
      <h4>We never touch your funds</h4>
      <p>SmartChama is only a facilitator. Your money stays within your group.We don’t hold or manage funds — we only 
        provide the system that keeps everything organized and visible.</p>
    </div>
    <div class="mcard reveal d2">
      <h4>Real-time accountability</h4>
      <p>Every transaction, contribution, and loan is recorded and visible to all members at any time.</p>
    </div>
    <div class="mcard reveal d3">
      <h4>Flexible payment options</h4>
      <p>Make and track contributions using M-PESA, PayPal, Wave or manual entries —
  giving your group the flexibility to operate both locally and across borders.</p>
    </div>
  </div>
</section>

<!-- SERVICES -->
<section class="services">
  <div class="services-header reveal">
    <span class="section-tag">What we offer</span>
    <h2 class="section-title">Our Services</h2>
    <p class="section-sub">Everything your chama needs to run smoothly — from contributions and loans to meetings, reports, and financial literacy.</p>
  </div>
  <div class="services-grid">
    <div class="scard reveal d1"><h4>Chama Management</h4><p>Full group management: accounting, meeting schedules, record keeping and member roles.</p></div>
    <div class="scard reveal d3"><h4>Social Welfare</h4><p>Manage welfare fund contributions, requests, approvals and payments systematically.</p></div>
    <div class="scard reveal d1"><h4>Loan Management</h4><p>Process loan applications, track repayments and calculate interest — all automated.</p></div>
    <div class="scard reveal d2"><h4>Financial Literacy</h4><p>Training resources to help group members understand savings, credit and wealth building.</p></div>
    <div class="scard reveal d3"><h4>Reports & Analytics</h4><p>Monthly statements, contribution summaries and group performance reports at a click.</p></div>
  </div>
</section>

<!-- WHY -->
<section class="why" id="why">
  <div class="why-inner">
    <div class="reveal">
      <span class="section-tag">Why SmartChama?</span>
      <h2 class="section-title">Built for trust, designed for growth</h2>
      <p class="section-sub">Our platform aims at enhancing transparency, simplifying operations, and helping members get the most out of their chamas.</p>
    </div>
    <div class="why-grid reveal">
      <div class="why-item"><div class="why-check">✓</div><div><h5>Safe &amp; Secure</h5><p>Your finances and data are fully protected.</p></div></div>
      <div class="why-item"><div class="why-check">✓</div><div><h5>Accountability</h5><p>Every detail is readily accessible to members.</p></div></div>
      <div class="why-item"><div class="why-check">✓</div><div><h5>Affordable</h5><p>The best rates with no hidden ledger costs.</p></div></div>
      <div class="why-item"><div class="why-check">✓</div><div><h5>Convenient</h5><p>Transact from anywhere, anytime via mobile.</p></div></div>
      <div class="why-item"><div class="why-check">✓</div><div><h5>Data Privacy</h5><p>We never share your group's data.</p></div></div>
      <div class="why-item"><div class="why-check">✓</div><div><h5>Easy to Use</h5><p>Intuitive and simple for all members.</p></div></div>
    </div>
  </div>
</section>

<!-- PRICING -->
<section class="pricing" id="pricing">
  <span class="section-tag reveal">Pricing</span>
  <h2 class="section-title reveal">Simple, transparent and affordable pricing</h2>
  <p class="section-sub reveal">Start free. Upgrade when your chama grows. All paid plans include a 14-day Premium trial.</p>
  <div class="pricing-grid">
    <div class="price-card reveal d1">
      <div class="price-name">Free</div>
      <div class="price-amount">KES 0</div>
      <div class="price-period">forever</div>
      <ul class="price-features">
        <li class="on"><i class="bi bi-check-circle-fill"></i> Up to 10 members</li>
        <li class="on"><i class="bi bi-check-circle-fill"></i> M-Pesa & PayPal</li>
        <li class="on"><i class="bi bi-check-circle-fill"></i> Contributions & loans</li>
        <li class="off"><i class="bi bi-x-circle"></i> PDF reports</li>
        <li class="off"><i class="bi bi-x-circle"></i> Email notifications</li>
        <li class="off"><i class="bi bi-x-circle"></i> Audit logs</li>
      </ul>
      <a href="/create-chama" class="btn-price">Get started</a>
    </div>
    <div class="price-card reveal d1">
      <div class="price-name">Basic</div>
      <div class="price-amount">KES 499</div>
      <div class="price-period">per month</div>
      <ul class="price-features">
        <li class="on"><i class="bi bi-check-circle-fill"></i> Up to 30 members</li>
        <li class="on"><i class="bi bi-check-circle-fill"></i> All payment methods</li>
        <li class="on"><i class="bi bi-check-circle-fill"></i> PDF reports</li>
        <li class="on"><i class="bi bi-check-circle-fill"></i> Email notifications</li>
        <li class="on"><i class="bi bi-check-circle-fill"></i> Audit logs</li>
        <li class="off"><i class="bi bi-x-circle"></i> Priority support</li>
      </ul>
      <a href="/create-chama" class="btn-price">Start free trial</a>
    </div>
    <div class="price-card price-card-featured reveal d2">
      <div class="price-name">Premium</div>
      <div class="price-amount">KES 999</div>
      <div class="price-period">per month</div>
      <ul class="price-features">
        <li class="on"><i class="bi bi-check-circle-fill"></i> Up to 100 members</li>
        <li class="on"><i class="bi bi-check-circle-fill"></i> All payment methods</li>
        <li class="on"><i class="bi bi-check-circle-fill"></i> PDF reports</li>
        <li class="on"><i class="bi bi-check-circle-fill"></i> Email notifications</li>
        <li class="on"><i class="bi bi-check-circle-fill"></i> Full audit logs</li>
        <li class="on"><i class="bi bi-check-circle-fill"></i> Priority support</li>
      </ul>
      <a href="/create-chama" class="btn-price btn-price-blue">Start free trial</a>
    </div>
    <div class="price-card reveal d3">
      <div class="price-name">Premium+</div>
      <div class="price-amount">KES 1,999</div>
      <div class="price-period">per month</div>
      <ul class="price-features">
        <li class="on"><i class="bi bi-check-circle-fill"></i> Unlimited members</li>
        <li class="on"><i class="bi bi-check-circle-fill"></i> Everything in Premium</li>
        <li class="on"><i class="bi bi-check-circle-fill"></i> Multiple chamas</li>
        <li class="on"><i class="bi bi-check-circle-fill"></i> Custom branding</li>
        <li class="on"><i class="bi bi-check-circle-fill"></i> API access</li>
        <li class="on"><i class="bi bi-check-circle-fill"></i> Priority support</li>
      </ul>
      <a href="/create-chama" class="btn-price">Start free trial</a>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="testimonials" id="testimonials">
  <h2 class="section-title reveal">Testimonials</h2>
  <div class="t-grid">
    <div class="tcard reveal d1">
      <p>"Managing our chama has never been this easy. All our records, contributions, and accounting are in real-time. We finally have full visibility."</p>
      <div class="t-author"><div class="t-avatar">WM</div><div><div class="t-name">Grace K Matere</div><div class="t-group">Journey ladies</div></div></div>
    </div>
</section>

<!-- WHO WE SERVE -->
<section class="clients reveal">
  <h3>Who we serve</h3>
  <div class="chips">
    <span class="chip">Investment Groups</span>
    <span class="chip">Self Help Groups</span>
    <span class="chip">Churches</span>
    <span class="chip">NGOs</span>
    <span class="chip">CBOs</span>
    <span class="chip">Table Banking Groups</span>
    <span class="chip">Diaspora Chamas</span>
    <span class="chip">Youth Groups</span>
  </div>
</section>

<!-- CTA -->
<section class="cta-section reveal">
  <h2>Start saving today.</h2>
  <p>Join thousands of Kenyan groups already managing their chama smarter with SmartChama.</p>
  <div class="cta-buttons">
    <a href="/create-chama" class="btn-white"><i class="bi bi-rocket-takeoff"></i> Create your chama account</a>
    <a href="mailto:joymutai@gmail.com" class="btn-ghost-white"><i class="bi bi-envelope"></i> Contact us</a>
  </div>
  <p class="cta-note">Free 14-day Premium trial ·</p>
</section>

<!-- FOOTER -->
<footer>
  <div>
    <div class="footer-logo">Smart<span>Chama</span></div>
    <p class="footer-tagline">Kenya's trusted digital platform for savings groups. Transparent, accountable, and built for communities.</p>
    <p class="footer-contact">
      <i class="bi bi-geo-alt"></i> Nairobi, Kenya<br>
      <i class="bi bi-envelope"></i>joymutai@gmail.com<br>
      <i class="bi bi-phone"></i> +254 742612073
    </p>
  </div>
  <div>
    <div class="footer-col-title">Product</div>
    <ul class="footer-links">
      <li><a href="#services">Features</a></li>
      <li><a href="#pricing">Pricing</a></li>
      <li><a href="#why">Why Us</a></li>
      <li><a href="/create-chama">Create Chama</a></li>
      <li><a href="/login">Sign In</a></li>
    </ul>
  </div>
  <div>
    <div class="footer-col-title">Support</div>
    <ul class="footer-links">
      <li><a href="#">Help Centre</a></li>
      <li><a href="#">Contact Us</a></li>
      <li><a href="#">WhatsApp Support</a></li>
      <li><a href="#">System Status</a></li>
    </ul>
  </div>
  <div>
    <div class="footer-col-title">Legal</div>
    <ul class="footer-links">
      <li><a href="#">Privacy Policy</a></li>
      <li><a href="#">Terms of Service</a></li>
      <li><a href="#">Cookie Policy</a></li>
      <li><a href="#">Security</a></li>
    </ul>
  </div>
</footer>
<div class="footer-bottom">
  <p>© 2026 SmartChama. All rights reserved.</p>
  <p>joymutai@gmail.com &nbsp;·&nbsp; +254 742612073</p>
</div>

<script>
  // Navbar scroll
  window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('scrolled', scrollY > 20);
  });

  // Mobile nav
  function toggleNav() {
    document.getElementById('mobileNav').classList.toggle('open');
  }

  // Slider
  let cur = 0;
  function goSlide(n) {
    cur = n;
    document.getElementById('slides').style.transform = 'translateX(-' + n + '00%)';
    document.querySelectorAll('.dot').forEach((d, i) => d.classList.toggle('active', i === n));
  }
  setInterval(() => goSlide((cur + 1) % 3), 4500);

  // Scroll reveal
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
  }, { threshold: 0.1 });
  document.querySelectorAll('.reveal').forEach(el => obs.observe(el));

  // Smooth anchor scroll
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const t = document.querySelector(a.getAttribute('href'));
      if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth' }); }
    });
  });
</script>
</body>
</html>