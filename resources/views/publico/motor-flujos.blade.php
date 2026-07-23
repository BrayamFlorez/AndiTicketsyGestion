<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Motor de Flujos · AndiSoftware · Documentación</title>
<meta name="description" content="Cómo funciona el motor de aprobaciones: conceptos, casos reales y participación por rol.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.13.0/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.0/dist/cdn.min.js"></script>
<style>
:root {
    --bg: #0d0d0f;
    --bg-2: #16161a;
    --bg-3: #1c1c22;
    --bg-4: #232329;
    --border: #2a2a32;
    --border-strong: #3a3a44;
    --accent: #007acc;
    --accent-2: #22a3ff;
    --accent-glow: rgba(0, 122, 204, 0.25);
    --warn: #f59e0b;
    --success: #22c55e;
    --danger: #ef4444;
    --text: #f5f5f7;
    --text-2: #a1a1aa;
    --text-3: #6b6b75;
    --grid: rgba(60, 60, 72, 0.35);
    --font-display: 'Space Grotesk', system-ui, sans-serif;
    --font-body: 'Inter', system-ui, sans-serif;
    --font-mono: 'JetBrains Mono', ui-monospace, monospace;
}

* { margin: 0; padding: 0; box-sizing: border-box; }
html { scroll-behavior: smooth; }
body {
    background: var(--bg);
    color: var(--text);
    font-family: var(--font-body);
    line-height: 1.55;
    font-size: 15px;
    overflow-x: hidden;
    min-height: 100vh;
}
body::before {
    content: '';
    position: fixed;
    inset: 0;
    background-image:
        linear-gradient(var(--grid) 1px, transparent 1px),
        linear-gradient(90deg, var(--grid) 1px, transparent 1px);
    background-size: 48px 48px;
    pointer-events: none;
    z-index: 0;
    mask: radial-gradient(ellipse 80% 60% at 50% 30%, black 0%, transparent 90%);
    -webkit-mask: radial-gradient(ellipse 80% 60% at 50% 30%, black 0%, transparent 90%);
}
.wrap {
    max-width: 1180px;
    margin: 0 auto;
    padding: 0 28px;
    position: relative;
    z-index: 1;
}

/* ─── Header ─────────────────────────────────────────────────── */
header {
    border-bottom: 1px solid var(--border);
    padding: 18px 0;
    position: sticky;
    top: 0;
    background: rgba(13, 13, 15, 0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    z-index: 100;
}
.header-row { display: flex; justify-content: space-between; align-items: center; }
.brand { display: flex; align-items: center; gap: 12px; }
.brand-mark {
    width: 32px; height: 32px;
    background: var(--accent);
    border-radius: 6px;
    display: grid; place-items: center;
    color: white; font-size: 14px;
    box-shadow: 0 0 0 1px rgba(255,255,255,0.05), 0 4px 12px var(--accent-glow);
}
.brand-name { font-family: var(--font-display); font-weight: 600; font-size: 14px; letter-spacing: -0.01em; }
.brand-sub { font-family: var(--font-mono); font-size: 10px; color: var(--text-3); letter-spacing: 0.05em; }
.header-meta {
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--text-3);
    letter-spacing: 0.05em;
    display: flex;
    align-items: center;
    gap: 16px;
}
.header-meta .dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--success);
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
    animation: pulse-dot 2s ease-in-out infinite;
}
@keyframes pulse-dot { 50% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0.08); } }

/* ─── Hero ───────────────────────────────────────────────────── */
.hero { padding: 60px 0 30px; }
.hero-intro {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 40px;
    align-items: end;
    margin-bottom: 40px;
}
.hero-title {
    font-family: var(--font-display);
    font-weight: 500;
    font-size: 52px;
    line-height: 1.02;
    letter-spacing: -0.03em;
    color: var(--text);
}
.hero-title em { font-style: normal; color: var(--text-3); font-weight: 400; }
.hero-meta {
    font-family: var(--font-mono);
    font-size: 12px;
    color: var(--text-3);
    text-align: right;
    line-height: 1.7;
    letter-spacing: 0.02em;
}
.hero-meta strong { color: var(--text); font-weight: 500; }

/* ─── Live simulator (INTACTO) ───────────────────────────────── */
.sim {
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
}
.sim-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    border-bottom: 1px solid var(--border);
    background: linear-gradient(180deg, var(--bg-3), var(--bg-2));
}
.sim-header-left { display: flex; align-items: center; gap: 20px; }
.sim-label {
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--text-3);
    letter-spacing: 0.12em;
    text-transform: uppercase;
}
.sim-doc { display: flex; align-items: center; gap: 10px; }
.sim-code { font-family: var(--font-mono); font-size: 12px; color: var(--accent-2); letter-spacing: 0.02em; }
.sim-title { font-family: var(--font-display); font-size: 15px; font-weight: 500; color: var(--text); }
.sim-state {
    font-family: var(--font-mono);
    font-size: 11px;
    padding: 4px 10px;
    border-radius: 4px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    transition: all 0.3s;
}
.sim-state.en_progreso { background: rgba(0, 122, 204, 0.12); color: var(--accent-2); border: 1px solid rgba(0, 122, 204, 0.3); }
.sim-state.requiere_correccion { background: rgba(245, 158, 11, 0.12); color: var(--warn); border: 1px solid rgba(245, 158, 11, 0.3); }
.sim-state.aprobado { background: rgba(34, 197, 94, 0.12); color: var(--success); border: 1px solid rgba(34, 197, 94, 0.3); }
.sim-state.cancelado { background: rgba(239, 68, 68, 0.12); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.3); }
.sim-rev { font-family: var(--font-mono); font-size: 11px; color: var(--text-3); }
.sim-rev strong { color: var(--text); font-weight: 500; }

.stages {
    padding: 40px 24px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    position: relative;
}
.stages::after {
    content: '';
    position: absolute;
    left: 24px; right: 24px; top: 50%;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--border) 15%, var(--border) 85%, transparent);
    z-index: 0; pointer-events: none;
}
.stage {
    background: var(--bg-3);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 20px;
    position: relative;
    z-index: 1;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}
.stage.active {
    border-color: var(--accent);
    background: var(--bg-4);
    box-shadow: 0 0 0 1px var(--accent-glow), 0 12px 40px rgba(0, 122, 204, 0.15);
    transform: translateY(-2px);
}
.stage.done { border-color: rgba(34, 197, 94, 0.35); }
.stage.rejected { border-color: rgba(239, 68, 68, 0.4); }
.stage-num { font-family: var(--font-mono); font-size: 10px; color: var(--text-3); letter-spacing: 0.15em; margin-bottom: 8px; }
.stage.active .stage-num { color: var(--accent-2); }
.stage.done .stage-num { color: var(--success); }
.stage.rejected .stage-num { color: var(--danger); }
.stage-name { font-family: var(--font-display); font-weight: 500; font-size: 15px; color: var(--text); margin-bottom: 4px; }
.stage-mode { font-family: var(--font-mono); font-size: 10px; color: var(--text-3); letter-spacing: 0.05em; margin-bottom: 16px; }
.actors { display: flex; flex-direction: column; gap: 6px; }
.actor {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 10px;
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: 6px;
    transition: all 0.3s;
}
.actor.pending.current {
    border-color: var(--accent);
    animation: pulse-actor 1.5s ease-in-out infinite;
}
@keyframes pulse-actor {
    0%, 100% { box-shadow: 0 0 0 0 rgba(0, 122, 204, 0.4); }
    50% { box-shadow: 0 0 0 4px rgba(0, 122, 204, 0.0); }
}
.actor.approved { border-color: rgba(34, 197, 94, 0.35); background: rgba(34, 197, 94, 0.06); }
.actor.rejected { border-color: rgba(239, 68, 68, 0.4); background: rgba(239, 68, 68, 0.06); }
.actor.superseded { opacity: 0.35; }
.actor-avatar {
    width: 22px; height: 22px;
    border-radius: 50%;
    display: grid; place-items: center;
    font-family: var(--font-display); font-weight: 600; font-size: 11px;
    background: var(--bg-4); color: var(--text-2);
    flex-shrink: 0;
}
.actor.approved .actor-avatar { background: var(--success); color: white; }
.actor.rejected .actor-avatar { background: var(--danger); color: white; }
.actor.pending.current .actor-avatar { background: var(--accent); color: white; }
.actor-name { font-size: 12px; color: var(--text); font-weight: 500; flex: 1; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.actor-status { font-family: var(--font-mono); font-size: 10px; color: var(--text-3); letter-spacing: 0.03em; }
.actor.approved .actor-status { color: var(--success); }
.actor.rejected .actor-status { color: var(--danger); }

.sim-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    border-top: 1px solid var(--border);
    background: var(--bg-3);
    flex-wrap: wrap;
    gap: 16px;
}
.sim-log { flex: 1; min-width: 0; font-family: var(--font-mono); font-size: 11px; color: var(--text-3); line-height: 1.6; max-height: 44px; overflow: hidden; position: relative; }
.sim-log-entry { animation: log-in 0.4s ease; }
@keyframes log-in { from { opacity: 0; transform: translateY(4px); } }
.sim-log-time { color: var(--accent-2); margin-right: 8px; }
.sim-buttons { display: flex; gap: 8px; flex-shrink: 0; }

.btn {
    padding: 8px 14px;
    border-radius: 6px;
    font-family: var(--font-display);
    font-size: 12px;
    font-weight: 500;
    letter-spacing: 0.02em;
    border: 1px solid var(--border-strong);
    background: var(--bg-4);
    color: var(--text);
    cursor: pointer;
    transition: all 0.15s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}
.btn:hover { border-color: var(--accent); color: var(--accent-2); }
.btn.solid { background: var(--accent); border-color: var(--accent); color: white; }
.btn.solid:hover { background: var(--accent-2); color: white; }
.btn.danger:hover { border-color: var(--danger); color: var(--danger); }
.btn:disabled { opacity: 0.4; cursor: not-allowed; }
.btn i { font-size: 10px; }

/* ─── Tabla de contenidos ─────────────────────────────────────── */
.toc {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 14px 20px;
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: 10px;
    margin-top: 32px;
    flex-wrap: wrap;
    font-family: var(--font-mono);
    font-size: 11px;
}
.toc-label { color: var(--text-3); letter-spacing: 0.1em; text-transform: uppercase; }
.toc a { color: var(--text-2); text-decoration: none; transition: color 0.15s; }
.toc a:hover { color: var(--accent-2); }

/* ─── Chapters ────────────────────────────────────────────────── */
section.chapter {
    padding: 80px 0 40px;
    position: relative;
    scroll-margin-top: 80px;
}
.chapter-head {
    display: grid;
    grid-template-columns: 90px 1fr;
    gap: 24px;
    margin-bottom: 48px;
    align-items: baseline;
}
.chapter-num {
    font-family: var(--font-mono);
    font-size: 12px;
    color: var(--text-3);
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
.chapter-num strong { color: var(--accent-2); font-weight: 500; }
.chapter-title {
    font-family: var(--font-display);
    font-weight: 500;
    font-size: 34px;
    letter-spacing: -0.02em;
    line-height: 1.1;
    margin-bottom: 12px;
}
.chapter-sub {
    color: var(--text-2);
    max-width: 640px;
    font-size: 15px;
    line-height: 1.65;
}
.chapter-sub b { color: var(--text); font-weight: 500; }

.subhead {
    font-family: var(--font-mono);
    font-size: 12px;
    color: var(--text-3);
    letter-spacing: 0.1em;
    text-transform: uppercase;
    margin: 48px 0 16px;
    padding-top: 8px;
    border-top: 1px dashed var(--border);
}

/* ─── Glosario ────────────────────────────────────────────────── */
.glossary {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}
.term {
    display: grid;
    grid-template-columns: 40px 1fr;
    gap: 16px;
    padding: 20px;
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: 10px;
    transition: border-color 0.2s;
}
.term:hover { border-color: var(--border-strong); }
.term-icon {
    width: 36px; height: 36px;
    border-radius: 8px;
    background: rgba(0, 122, 204, 0.08);
    border: 1px solid rgba(0, 122, 204, 0.2);
    color: var(--accent-2);
    display: grid; place-items: center;
    font-size: 13px;
}
.term-name {
    font-family: var(--font-display);
    font-weight: 500;
    font-size: 15px;
    margin-bottom: 4px;
    color: var(--text);
}
.term-alias {
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--text-3);
    font-weight: 400;
    margin-left: 6px;
}
.term-def {
    font-size: 13px;
    color: var(--text-2);
    line-height: 1.55;
    margin-bottom: 6px;
}
.term-eg {
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--text-3);
    line-height: 1.55;
}
.term-eg code {
    background: var(--bg-3);
    padding: 1px 5px;
    border-radius: 3px;
    color: var(--accent-2);
}
.term-eg em { color: var(--text-2); font-style: normal; }

/* ─── Escenas del caso real ──────────────────────────────────── */
.scenes {
    display: flex;
    flex-direction: column;
    gap: 0;
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 0 32px;
}
.scene {
    display: grid;
    grid-template-columns: 100px 1fr;
    gap: 32px;
    padding: 28px 0;
    border-top: 1px dashed var(--border);
    position: relative;
}
.scene:first-child { border-top: none; }
.scene-time {
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--text-3);
    letter-spacing: 0.05em;
    padding-top: 4px;
}
.scene-title {
    font-family: var(--font-display);
    font-weight: 500;
    font-size: 17px;
    color: var(--text);
    margin-bottom: 8px;
}
.scene-text {
    color: var(--text-2);
    font-size: 14px;
    line-height: 1.65;
    margin-bottom: 10px;
}
.scene-text b { color: var(--text); font-weight: 500; }
.scene-meta {
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--text-3);
    padding: 8px 12px;
    background: var(--bg-3);
    border-left: 2px solid var(--border);
    border-radius: 0 4px 4px 0;
    line-height: 1.6;
}
.scene-meta code { color: var(--accent-2); background: rgba(0, 122, 204, 0.08); padding: 1px 5px; border-radius: 3px; }
.scene-meta b { color: var(--text-2); font-weight: 500; }
.scene.reject .scene-time { color: var(--danger); }
.scene.reject .scene-meta { border-left-color: rgba(239, 68, 68, 0.4); }
.scene.approve .scene-time { color: var(--success); }
.scene.approve .scene-meta { border-left-color: rgba(34, 197, 94, 0.4); }
.scene.correct .scene-time { color: var(--warn); }
.scene.correct .scene-meta { border-left-color: rgba(245, 158, 11, 0.4); }

/* ─── Roles ──────────────────────────────────────────────────── */
.roles {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}
.role {
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 28px 24px;
    display: flex;
    flex-direction: column;
}
.role-icon {
    width: 44px; height: 44px;
    border-radius: 10px;
    background: rgba(0, 122, 204, 0.08);
    border: 1px solid rgba(0, 122, 204, 0.2);
    color: var(--accent-2);
    display: grid; place-items: center;
    font-size: 16px;
    margin-bottom: 20px;
}
.role-tag {
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--text-3);
    letter-spacing: 0.12em;
    margin-bottom: 6px;
}
.role-name {
    font-family: var(--font-display);
    font-weight: 500;
    font-size: 17px;
    color: var(--text);
    margin-bottom: 16px;
}
.role-list { list-style: none; padding: 0; margin: 0; }
.role-list li {
    font-size: 13px;
    color: var(--text-2);
    line-height: 1.55;
    padding: 10px 0 10px 20px;
    border-top: 1px solid var(--border);
    position: relative;
}
.role-list li:first-child { border-top: none; }
.role-list li::before {
    content: '';
    position: absolute;
    left: 0; top: 17px;
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--accent);
}
.role-list li b { color: var(--text); font-weight: 500; }

/* ─── Trio (configuración) ───────────────────────────────────── */
.trio { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.trio-card {
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 28px 24px;
    position: relative;
    transition: border-color 0.2s;
}
.trio-card:hover { border-color: var(--border-strong); }
.trio-card::before {
    content: attr(data-step);
    font-family: var(--font-mono);
    font-size: 10px;
    color: var(--text-3);
    position: absolute;
    top: 20px; right: 20px;
    letter-spacing: 0.1em;
}
.trio-icon {
    width: 40px; height: 40px;
    border-radius: 8px;
    background: rgba(0, 122, 204, 0.08);
    border: 1px solid rgba(0, 122, 204, 0.2);
    color: var(--accent-2);
    display: grid; place-items: center;
    font-size: 14px;
    margin-bottom: 24px;
}
.trio-name { font-family: var(--font-display); font-weight: 500; font-size: 17px; margin-bottom: 8px; }
.trio-desc { font-size: 13px; color: var(--text-2); line-height: 1.6; }

/* ─── Modes ──────────────────────────────────────────────────── */
.modes { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
.mode { background: var(--bg-2); border: 1px solid var(--border); border-radius: 12px; padding: 24px; display: flex; flex-direction: column; }
.mode-viz {
    height: 100px;
    display: flex; align-items: center; justify-content: center;
    background: var(--bg-3);
    border-radius: 8px;
    margin-bottom: 20px;
    gap: 8px;
    position: relative;
    overflow: hidden;
}
.mode-actor {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: var(--bg-4);
    border: 1.5px solid var(--accent);
    color: var(--accent-2);
    display: grid; place-items: center;
    font-size: 12px;
    position: relative; z-index: 1;
}
.mode-op { font-family: var(--font-mono); color: var(--text-3); font-size: 16px; }
.mode-name { font-family: var(--font-display); font-weight: 500; font-size: 16px; margin-bottom: 6px; }
.mode-key { font-family: var(--font-mono); font-size: 11px; color: var(--text-3); letter-spacing: 0.02em; margin-bottom: 12px; }
.mode-desc { font-size: 13px; color: var(--text-2); line-height: 1.55; }

/* ─── Policies ───────────────────────────────────────────────── */
.policies { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
.policy {
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-left: 2px solid var(--warn);
    border-radius: 8px;
    padding: 20px 24px;
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 20px;
    align-items: start;
}
.policy-key { font-family: var(--font-mono); font-size: 10px; color: var(--warn); letter-spacing: 0.05em; padding-top: 3px; min-width: 100px; }
.policy-name { font-family: var(--font-display); font-weight: 500; font-size: 15px; color: var(--text); margin-bottom: 4px; }
.policy-desc { font-size: 13px; color: var(--text-2); line-height: 1.55; }

/* ─── Features ───────────────────────────────────────────────── */
.features { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
.feature { background: var(--bg-2); border: 1px solid var(--border); border-radius: 12px; padding: 24px; }
.feature-icon {
    width: 36px; height: 36px;
    border-radius: 8px;
    background: rgba(245, 158, 11, 0.1);
    border: 1px solid rgba(245, 158, 11, 0.2);
    color: var(--warn);
    display: grid; place-items: center;
    font-size: 14px;
    margin-bottom: 16px;
}
.feature-name { font-family: var(--font-display); font-weight: 500; font-size: 15px; margin-bottom: 6px; }
.feature-desc { font-size: 13px; color: var(--text-2); line-height: 1.55; }
.bell-ring { display: inline-block; animation: bell-ring 2.5s ease-in-out infinite; transform-origin: top center; }
@keyframes bell-ring {
    0%, 60%, 100% { transform: rotate(0); }
    65% { transform: rotate(-12deg); } 75% { transform: rotate(10deg); }
    85% { transform: rotate(-6deg); } 95% { transform: rotate(4deg); }
}

/* ─── FAQ ────────────────────────────────────────────────────── */
.faq { display: flex; flex-direction: column; gap: 8px; }
.faq-item {
    background: var(--bg-2);
    border: 1px solid var(--border);
    border-radius: 10px;
    overflow: hidden;
    transition: border-color 0.2s;
}
.faq-item.open { border-color: var(--accent); }
.faq-q {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    padding: 18px 24px;
    background: none;
    border: none;
    color: var(--text);
    font-family: var(--font-display);
    font-size: 15px;
    font-weight: 500;
    text-align: left;
    cursor: pointer;
    transition: color 0.15s;
}
.faq-q:hover { color: var(--accent-2); }
.faq-toggle { color: var(--text-3); font-size: 12px; transition: transform 0.2s; }
.faq-item.open .faq-toggle { transform: rotate(45deg); color: var(--accent-2); }
.faq-a { padding: 0 24px 20px; color: var(--text-2); font-size: 14px; line-height: 1.65; }
.faq-a code {
    font-family: var(--font-mono);
    font-size: 12px;
    background: var(--bg-3);
    padding: 2px 6px;
    border-radius: 4px;
    color: var(--accent-2);
}
.faq-a b { color: var(--text); font-weight: 500; }
.faq-a em { color: var(--accent-2); font-style: normal; }

/* ─── Footer ─────────────────────────────────────────────────── */
footer {
    padding: 40px 0 60px;
    border-top: 1px solid var(--border);
    margin-top: 80px;
    font-family: var(--font-mono);
    font-size: 11px;
    color: var(--text-3);
    letter-spacing: 0.02em;
}
.footer-row { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }

/* ─── Reveal on scroll ──────────────────────────────────────── */
.reveal {
    opacity: 0;
    transform: translateY(16px);
    transition: opacity 0.7s cubic-bezier(0.4, 0, 0.2, 1), transform 0.7s cubic-bezier(0.4, 0, 0.2, 1);
}
.reveal.visible { opacity: 1; transform: translateY(0); }

/* ─── Responsive ──────────────────────────────────────────── */
@media (max-width: 900px) {
    .hero-title { font-size: 36px; }
    .hero-intro { grid-template-columns: 1fr; gap: 20px; }
    .hero-meta { text-align: left; }
    .stages, .trio, .modes, .features, .roles, .glossary { grid-template-columns: 1fr; }
    .stages::after { display: none; }
    .policies { grid-template-columns: 1fr; }
    .chapter-title { font-size: 26px; }
    .chapter-head { grid-template-columns: 1fr; gap: 8px; margin-bottom: 32px; }
    .sim-header { flex-direction: column; align-items: flex-start; gap: 12px; padding: 16px; }
    .sim-doc { flex-wrap: wrap; }
    .sim-controls { padding: 16px; }
    .sim-log { max-height: none; }
    .scene { grid-template-columns: 1fr; gap: 12px; }
    .scenes { padding: 0 20px; }
    .toc { gap: 12px; }
}
@media (prefers-reduced-motion: reduce) {
    * { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
    .reveal { opacity: 1; transform: none; }
}
</style>
</head>
<body>

<header>
    <div class="wrap header-row">
        <div class="brand">
            <div class="brand-mark"><i class="fas fa-code-branch"></i></div>
            <div>
                <div class="brand-name">Motor de Flujos</div>
                <div class="brand-sub">ANDISOFTWARE</div>
            </div>
        </div>
        <div class="header-meta">
            <span><span class="dot"></span> Documentación · v1.0</span>
        </div>
    </div>
</header>

<div class="wrap">

<!-- ═══════════════════════════════════════════════════════ HERO ═══ -->
<section class="hero">
    <div class="hero-intro">
        <h1 class="hero-title">
            Motor de aprobaciones<br>
            <em>diseñado como un instrumento.</em>
        </h1>
        <div class="hero-meta">
            <strong>Sistema</strong>&nbsp;·&nbsp;Generador de procesos<br>
            <strong>Permisos</strong>&nbsp;·&nbsp;Spatie<br>
            <strong>Canales</strong>&nbsp;·&nbsp;mail + database
        </div>
    </div>

    <!-- Live simulator -->
    <div class="sim" x-data="motor()" x-init="init()">
        <div class="sim-header">
            <div class="sim-header-left">
                <span class="sim-label">Trámite en curso</span>
                <div class="sim-doc">
                    <span class="sim-code" x-text="codigo"></span>
                    <span class="sim-title">Cotización cliente ACME</span>
                </div>
            </div>
            <div style="display: flex; gap: 16px; align-items: center;">
                <span class="sim-rev">Rev. <strong x-text="revision"></strong></span>
                <span class="sim-state" :class="estado" x-text="etiquetaEstado()"></span>
            </div>
        </div>

        <div class="stages">
            <template x-for="(etapa, idx) in etapas" :key="idx">
                <div class="stage"
                     :class="{ active: idx === etapaActual && !cerrado(), done: idx < etapaActual, rejected: etapa.rechazada }">
                    <div class="stage-num">
                        <span x-text="`ETAPA · 0${idx+1}/03`"></span>
                    </div>
                    <div class="stage-name" x-text="etapa.nombre"></div>
                    <div class="stage-mode" x-text="etapa.modoLabel"></div>
                    <div class="actors">
                        <template x-for="(actor, aIdx) in etapa.actores" :key="aIdx">
                            <div class="actor"
                                 :class="{
                                    pending: actor.estado === 'pendiente',
                                    current: actor.estado === 'pendiente' && idx === etapaActual,
                                    approved: actor.estado === 'aprobado',
                                    rejected: actor.estado === 'rechazado',
                                    superseded: actor.estado === 'superseded'
                                 }">
                                <span class="actor-avatar" x-text="actor.iniciales"></span>
                                <span class="actor-name" x-text="actor.nombre"></span>
                                <span class="actor-status" x-text="actor.estadoLabel()"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <div class="sim-controls">
            <div class="sim-log">
                <template x-for="(ev, i) in eventos.slice(-2)" :key="ev.id">
                    <div class="sim-log-entry">
                        <span class="sim-log-time" x-text="ev.time"></span>
                        <span x-text="ev.msg"></span>
                    </div>
                </template>
            </div>
            <div class="sim-buttons">
                <button class="btn" @click="pausar()" x-show="!cerrado()">
                    <i class="fas" :class="autoplay ? 'fa-pause' : 'fa-play'"></i>
                    <span x-text="autoplay ? 'Pausar' : 'Reanudar'"></span>
                </button>
                <button class="btn danger" @click="rechazar()" x-show="!cerrado() && !simulandoRechazo">
                    <i class="fas fa-times"></i> Simular rechazo
                </button>
                <button class="btn solid" @click="reiniciar()">
                    <i class="fas fa-redo"></i> Reiniciar
                </button>
            </div>
        </div>
    </div>

    <!-- Table of contents -->
    <nav class="toc">
        <span class="toc-label">EN ESTA PÁGINA:</span>
        <a href="#glosario">Glosario</a>
        <a href="#caso">Caso real</a>
        <a href="#participacion">Tu participación</a>
        <a href="#capas">Bajo el capó</a>
        <a href="#preguntas">Preguntas</a>
    </nav>
</section>

<!-- ═══════════════════════════════════════════════════ GLOSARIO ═══ -->
<section class="chapter reveal" id="glosario">
    <div class="chapter-head">
        <div class="chapter-num">GLOSARIO<br>12&nbsp;términos</div>
        <div>
            <h2 class="chapter-title">Diccionario del sistema</h2>
            <p class="chapter-sub">Comienza por aquí. Estos son <b>los 12 términos que utiliza el módulo</b>, cada uno con una definición breve y un ejemplo concreto. Si algún concepto de la simulación no te resulta claro durante su uso, aquí encontrarás la explicación detallada.</p>
        </div>
    </div>

    <div class="glossary">

        <div class="term">
            <div class="term-icon"><i class="fas fa-project-diagram"></i></div>
            <div class="term-body">
                <div class="term-name">Flujo <span class="term-alias">o Trámite</span></div>
                <div class="term-def">Un caso específico que transita por el proceso de aprobación. Es la unidad principal del sistema: <b>cada flujo es un expediente que se crea, avanza y concluye</b>.</div>
                <div class="term-eg">Cada uno tiene un código único como <code>FLJ-2026-000127</code>.</div>
            </div>
        </div>

        <div class="term">
            <div class="term-icon"><i class="fas fa-file-alt"></i></div>
            <div class="term-body">
                <div class="term-name">Tipo de documento</div>
                <div class="term-def">La categoría del trámite. Al iniciar un flujo, primero seleccionas su tipo; esto determina qué plantilla se aplicará automáticamente al proceso.</div>
                <div class="term-eg"><em>Cotización, Contrato, Autorización de compra.</em></div>
            </div>
        </div>

        <div class="term">
            <div class="term-icon"><i class="fas fa-map"></i></div>
            <div class="term-body">
                <div class="term-name">Plantilla de flujo</div>
                <div class="term-def">El <b>diseño predeterminado y reutilizable</b>: establece las etapas del proceso, su orden, las reglas operativas y quién está autorizado para firmar en cada una. El administrador la configura una sola vez.</div>
                <div class="term-eg"><em>"Aprobación de Cotización v1" · 3 etapas.</em></div>
            </div>
        </div>

        <div class="term">
            <div class="term-icon"><i class="fas fa-flag"></i></div>
            <div class="term-body">
                <div class="term-name">Etapa</div>
                <div class="term-def">Un <b>paso dentro del flujo</b> donde un usuario debe tomar una decisión (aprobar, rechazar o devolver). Las etapas están ordenadas de forma secuencial; cuando una concluye positivamente, el flujo avanza a la siguiente.</div>
                <div class="term-eg"><em>Revisión Técnica → Aprobación Financiera → Firma Final.</em></div>
            </div>
        </div>

        <div class="term">
            <div class="term-icon"><i class="fas fa-user-tag"></i></div>
            <div class="term-body">
                <div class="term-name">Actor</div>
                <div class="term-def"><b>La persona autorizada para decidir en una etapa.</b> Puede abarcar un rol completo (cualquier usuario que posea dicho rol en el sistema) o un individuo específico. Una misma etapa puede involucrar a múltiples actores.</div>
                <div class="term-eg"><em>Rol "técnico" · Usuario "María Torres"</em></div>
            </div>
        </div>

        <div class="term">
            <div class="term-icon"><i class="fas fa-play-circle"></i></div>
            <div class="term-body">
                <div class="term-name">Iniciador</div>
                <div class="term-def">El usuario que crea el flujo. Mantiene la <b>propiedad del trámite hasta su conclusión</b>: puede desistir en cualquier momento y, si un aprobador emite un rechazo, es el responsable exclusivo de corregir y reenviar la solicitud.</div>
                <div class="term-eg"><em>Suele ser el operador de ventas o el usuario final.</em></div>
            </div>
        </div>

        <div class="term">
            <div class="term-icon"><i class="fas fa-cogs"></i></div>
            <div class="term-body">
                <div class="term-name">Modo de etapa</div>
                <div class="term-def">Define cómo se resuelve una etapa cuando intervienen varios actores. Existen tres opciones: <b>secuencial</b> (un solo actor decide), <b>paralelo-todos</b> (requiere la firma de todos) o <b>paralelo-cualquiera</b> (es suficiente con una sola firma).</div>
                <div class="term-eg"><em>"Requiere dos firmas de administrador" = paralelo-todos.</em></div>
            </div>
        </div>

        <div class="term">
            <div class="term-icon"><i class="fas fa-undo"></i></div>
            <div class="term-body">
                <div class="term-name">Política de rechazo</div>
                <div class="term-def">Determina qué ocurre con el flujo ante una negación en una etapa: <b>devolver al iniciador</b>, retroceder a la etapa anterior, saltar a una etapa específica o finalizar de manera definitiva. Se configura por etapa y nunca actúa de forma implícita.</div>
                <div class="term-eg"><em>Ningún rechazo cancela el flujo de manera accidental.</em></div>
            </div>
        </div>

        <div class="term">
            <div class="term-icon"><i class="fas fa-history"></i></div>
            <div class="term-body">
                <div class="term-name">Revisión</div>
                <div class="term-def">El <b>número de iteración del flujo</b>. Comienza en 1 y se incrementa cada vez que el iniciador corrige y reenvía el trámite tras un rechazo. Permite agrupar los cambios y documentos adjuntos por cada intento o versión.</div>
                <div class="term-eg"><em>Rev. 1 → rechazo → corrección → Rev. 2.</em></div>
            </div>
        </div>

        <div class="term">
            <div class="term-icon"><i class="fas fa-hand-paper"></i></div>
            <div class="term-body">
                <div class="term-name">Asignación</div>
                <div class="term-def">La <b>decisión pendiente</b> que corresponde a un actor. Permanece en su bandeja de entrada hasta ser resuelta. Cada firma requerida constituye una asignación independiente en el sistema.</div>
                <div class="term-eg"><em>"María tiene una asignación pendiente en Revisión Técnica."</em></div>
            </div>
        </div>

        <div class="term">
            <div class="term-icon"><i class="fas fa-paperclip"></i></div>
            <div class="term-body">
                <div class="term-name">Adjuntos</div>
                <div class="term-def">Los <b>documentos del expediente</b>. Pueden registrarse a nivel del trámite (cargados por el iniciador como parte del expediente base) o a nivel de la etapa (subidos por el actor que aprueba como soporte de su decisión).</div>
                <div class="term-eg"><em>Cotización.pdf · Aprobación_finanzas.jpg</em></div>
            </div>
        </div>

        <div class="term">
            <div class="term-icon"><i class="fas fa-inbox"></i></div>
            <div class="term-body">
                <div class="term-name">Bandeja</div>
                <div class="term-def">El <b>panel de control donde cada usuario visualiza sus tareas</b>: las asignaciones pendientes, los flujos iniciados y aquellos que requieren corrección. Cuenta con pestañas y un buscador para localizar fácilmente cualquier expediente.</div>
                <div class="term-eg"><em>Mis Pendientes · Iniciados por mí · Cerrados.</em></div>
            </div>
        </div>

    </div>
</section>

<!-- ═══════════════════════════════════════════════ CASO PRÁCTICO ═══ -->
<section class="chapter reveal" id="caso">
    <div class="chapter-head">
        <div class="chapter-num">CASO<br>práctico·001</div>
        <div>
            <h2 class="chapter-title">Un caso práctico, paso a paso</h2>
            <p class="chapter-sub">Acompañemos a <b>Camila Ríos</b>, del área comercial, mientras tramita una cotización para el cliente ACME. Cada escena representa un momento del sistema; aquí podrá observar cómo se integran los términos del glosario en una situación operativa real.</p>
        </div>
    </div>

    <div class="scenes">

        <div class="scene">
            <div class="scene-time">LUN<br>10:14</div>
            <div>
                <div class="scene-title">Camila requiere cotizar $48M para ACME</div>
                <div class="scene-text">Ingresa al sistema, selecciona <b>"Iniciar flujo"</b>, elige el tipo <b>Cotización</b> y completa el título junto con la descripción. Adjunta el borrador de la propuesta en formato PDF.</div>
                <div class="scene-meta">Resultado: se crea el trámite <code>FLJ-2026-000127</code>, en <b>Rev. 1</b>, estado <b>en progreso</b>. El adjunto queda registrado a nivel del expediente.</div>
            </div>
        </div>

        <div class="scene">
            <div class="scene-time">LUN<br>10:15</div>
            <div>
                <div class="scene-title">El motor configura la primera etapa</div>
                <div class="scene-text">La plantilla establece que la Etapa 1 es <b>Revisión Técnica</b>, asignada al rol <b>técnico</b>. Dado que hay dos técnicos activos en el sistema (Juan T. y María R.), ambos reciben la <b>asignación</b>. La primera firma registrada cerrará la etapa.</div>
                <div class="scene-meta">Notificación por correo electrónico y alerta en la plataforma enviadas simultáneamente a Juan T. y María R.</div>
            </div>
        </div>

        <div class="scene approve">
            <div class="scene-time">LUN<br>11:02</div>
            <div>
                <div class="scene-title">Juan revisa y aprueba</div>
                <div class="scene-text">Juan ingresa al detalle, visualiza la propuesta y selecciona <b>Aprobar</b> añadiendo el comentario "Alcance OK". Como esta etapa está configurada en modo <b>secuencial</b>, una sola firma es suficiente para avanzar.</div>
                <div class="scene-meta">La asignación de María se cierra automáticamente con el estado "superada". El flujo avanza a la <b>Etapa 2 · Aprobación Financiera</b>.</div>
            </div>
        </div>

        <div class="scene">
            <div class="scene-time">LUN<br>14:30</div>
            <div>
                <div class="scene-title">La etapa financiera requiere dos firmas</div>
                <div class="scene-text">Ana G. y Roberto M., ambos con rol de administrador, reciben sus respectivas asignaciones. La etapa se encuentra en modo <b>paralelo-todos</b>: <b>no avanzará hasta que ambos registren su aprobación</b>. Ana aprueba a las 14:45 tras adjuntar su matriz de márgenes a la etapa.</div>
                <div class="scene-meta">La cotización permanece en la Etapa 2 a la espera de Roberto. Ana ya no visualiza esta tarea en sus pendientes.</div>
            </div>
        </div>

        <div class="scene reject">
            <div class="scene-time">LUN<br>15:12</div>
            <div>
                <div class="scene-title">Roberto rechaza: margen no adecuado</div>
                <div class="scene-text">Agrega el comentario obligatorio: "Descuento superior al permitido; corregir a un máximo del 15%". La etapa tiene configurada la política de <b>devolver al iniciador</b>, por lo que el trámite pasa al estado <b>requiere corrección</b>.</div>
                <div class="scene-meta">Camila recibe una notificación con el motivo del rechazo. La firma de Ana queda registrada en la Rev. 1, manteniendo el historial intacto.</div>
            </div>
        </div>

        <div class="scene correct">
            <div class="scene-time">MAR<br>09:00</div>
            <div>
                <div class="scene-title">Camila corrige y reenvía</div>
                <div class="scene-text">Lee el motivo de rechazo en su bandeja, dentro de la pestaña <b>"Requieren corrección"</b>. Ajusta el precio, sube el nuevo PDF y utiliza la opción <b>"Reenviar corregido"</b>. El flujo avanza a la <b>Rev. 2</b> y reinicia desde la Etapa 1 con nuevas asignaciones.</div>
                <div class="scene-meta">La Rev. 1 permanece intacta y disponible para consulta: cada archivo de esa iteración se puede descargar como parte del historial de auditoría.</div>
            </div>
        </div>

        <div class="scene approve">
            <div class="scene-time">MAR<br>16:45</div>
            <div>
                <div class="scene-title">Firma ejecutiva: cotización finalizada</div>
                <div class="scene-text">La Rev. 2 es procesada por Juan (técnica) → Ana y Roberto (financiera, ambos aprueban en esta ocasión) → jefe Oscar (firma final). El trámite concluye sin objeciones.</div>
                <div class="scene-meta">Estado final: <b>aprobado</b>. Camila recibe la notificación de cierre. Toda la línea de tiempo, incluyendo actores, fechas, comentarios y adjuntos, queda archivada en el detalle del flujo.</div>
            </div>
        </div>

    </div>
</section>

<!-- ═══════════════════════════════════════════════ TU ROL ═══ -->
<section class="chapter reveal" id="participacion">
    <div class="chapter-head">
        <div class="chapter-num">TU<br>participación</div>
        <div>
            <h2 class="chapter-title">Tú, en el sistema</h2>
            <p class="chapter-sub">Dependiendo de tu perfil de usuario, tienes acceso a diferentes funciones. Todos <b>coexistimos en el mismo sistema</b>, pero cada persona tiene una experiencia adaptada a su rol. Estos son los tres tipos de participación.</p>
        </div>
    </div>

    <div class="roles">

        <div class="role">
            <div class="role-icon"><i class="fas fa-play-circle"></i></div>
            <div class="role-tag">USUARIO/INICIADOR</div>
            <div class="role-name">Inicias y corriges</div>
            <ul class="role-list">
                <li><b>Inicias</b> flujos seleccionando el tipo de documento adecuado.</li>
                <li><b>Adjuntas</b> el expediente base (el archivo central del trámite).</li>
                <li><b>Haces seguimiento</b> del avance desde la pestaña "Iniciados por mí".</li>
                <li><b>Corriges</b> y reenvías el trámite si un aprobador solicita modificaciones.</li>
                <li><b>Desistes</b> si el trámite ya no es necesario.</li>
            </ul>
        </div>

        <div class="role">
            <div class="role-icon"><i class="fas fa-gavel"></i></div>
            <div class="role-tag">TÉCNICO/ADMIN</div>
            <div class="role-name">Apruebas o rechazas</div>
            <ul class="role-list">
                <li><b>Recibes</b> una notificación cuando se te asigna una decisión.</li>
                <li><b>Apruebas</b>, rechazas o devuelves el trámite añadiendo un comentario.</li>
                <li><b>Adjuntas</b> documentos de soporte si la etapa lo requiere.</li>
                <li><b>Revisas</b> la pestaña "Mis Pendientes" con todas tus asignaciones actuales.</li>
                <li>No puedes modificar el contenido del trámite; tu rol es tomar decisiones sobre él.</li>
            </ul>
        </div>

        <div class="role">
            <div class="role-icon"><i class="fas fa-shield-alt"></i></div>
            <div class="role-tag">ADMINISTRADOR</div>
            <div class="role-name">Diseñas y gestionas excepciones</div>
            <ul class="role-list">
                <li><b>Diseñas</b> las plantillas y los tipos de documentos disponibles.</li>
                <li><b>Visualizas</b> todos los flujos del sistema a nivel global.</li>
                <li><b>Cancelas</b> flujos que han quedado estancados o que ya no son válidos.</li>
                <li><b>Reabres</b> aquellos procesos que fueron cancelados por error.</li>
                <li><b>Actúas</b> como vía de solución ante bloqueos, manteniendo intacto el historial de auditoría.</li>
            </ul>
        </div>

    </div>
</section>

<!-- ═══════════════════════════════════════════════ BAJO EL CAPÓ ═══ -->
<section class="chapter reveal" id="capas">
    <div class="chapter-head">
        <div class="chapter-num">CAPAS<br>internas·03</div>
        <div>
            <h2 class="chapter-title">Bajo el capó</h2>
            <p class="chapter-sub">Para comprender <b>la estructura interna del sistema</b>. Todo está dividido en tres capas interconectadas: la configuración inicial, la ejecución del motor y la interacción a través de notificaciones y alertas.</p>
        </div>
    </div>

    <p class="subhead">CAPA/01 · Configuración — parámetros establecidos por el administrador</p>
    <div class="trio">
        <div class="trio-card" data-step="01/03">
            <div class="trio-icon"><i class="fas fa-file-alt"></i></div>
            <div class="trio-name">Tipos de documento</div>
            <div class="trio-desc">Catálogo de los trámites disponibles. Cada tipo está asociado a una plantilla de flujo predeterminada.</div>
        </div>
        <div class="trio-card" data-step="02/03">
            <div class="trio-icon"><i class="fas fa-project-diagram"></i></div>
            <div class="trio-name">Plantillas</div>
            <div class="trio-desc">Secuencia ordenada de etapas y sus reglas operativas: modo de ejecución, políticas de rechazo y requisitos de adjuntos.</div>
        </div>
        <div class="trio-card" data-step="03/03">
            <div class="trio-icon"><i class="fas fa-users"></i></div>
            <div class="trio-name">Actores por etapa</div>
            <div class="trio-desc">Define quién está autorizado para aprobar cada etapa (por rol Spatie o usuario específico). Múltiples actores habilitan la ejecución en paralelo.</div>
        </div>
    </div>

    <p class="subhead">CAPA/02 · Ejecución — lógica del motor de flujos</p>
    <div class="modes" style="margin-bottom: 32px;">
        <div class="mode">
            <div class="mode-viz">
                <div class="mode-actor"><i class="fas fa-user"></i></div>
            </div>
            <div class="mode-name">Secuencial</div>
            <div class="mode-key">secuencial</div>
            <div class="mode-desc">Un único actor toma la decisión. La etapa avanza al registrar su aprobación.</div>
        </div>
        <div class="mode">
            <div class="mode-viz">
                <div class="mode-actor"><i class="fas fa-user"></i></div>
                <span class="mode-op">+</span>
                <div class="mode-actor"><i class="fas fa-user"></i></div>
                <span class="mode-op">+</span>
                <div class="mode-actor"><i class="fas fa-user"></i></div>
            </div>
            <div class="mode-name">Paralelo · Todos</div>
            <div class="mode-key">paralelo_todos</div>
            <div class="mode-desc">Requiere la aprobación de todos los actores involucrados. Un solo rechazo detiene el avance.</div>
        </div>
        <div class="mode">
            <div class="mode-viz">
                <div class="mode-actor"><i class="fas fa-user"></i></div>
                <span class="mode-op">/</span>
                <div class="mode-actor"><i class="fas fa-user"></i></div>
                <span class="mode-op">/</span>
                <div class="mode-actor"><i class="fas fa-user"></i></div>
            </div>
            <div class="mode-name">Paralelo · Cualquiera</div>
            <div class="mode-key">paralelo_cualquiera</div>
            <div class="mode-desc">Es suficiente con la primera aprobación registrada. Las asignaciones restantes se cierran automáticamente.</div>
        </div>
    </div>

    <div class="policies">
        <div class="policy">
            <div class="policy-key">devolver_<br>iniciador</div>
            <div>
                <div class="policy-name">Devolver al iniciador</div>
                <div class="policy-desc">El flujo cambia a estado "requiere corrección". Al ser reenviado, el número de revisión se incrementa y el proceso reinicia desde la primera etapa.</div>
            </div>
        </div>
        <div class="policy">
            <div class="policy-key">devolver_<br>etapa_anterior</div>
            <div>
                <div class="policy-name">Devolver a etapa anterior</div>
                <div class="policy-desc">Se genera un nuevo paso en la etapa inmediatamente anterior con nuevas asignaciones para los usuarios.</div>
            </div>
        </div>
        <div class="policy">
            <div class="policy-key">devolver_<br>etapa_especifica</div>
            <div>
                <div class="policy-name">Devolver a etapa específica</div>
                <div class="policy-desc">El proceso retorna a una etapa previamente definida en la configuración de la plantilla. Ideal para retrocesos amplios.</div>
            </div>
        </div>
        <div class="policy">
            <div class="policy-key">finalizar</div>
            <div>
                <div class="policy-name">Finalizar</div>
                <div class="policy-desc">Es la única política que cierra definitivamente el flujo ante un rechazo. Diseñada para procesos donde una negación concluye el trámite.</div>
            </div>
        </div>
    </div>

    <p class="subhead">CAPA/03 · Interacción — visibilidad y notificaciones</p>
    <div class="features">
        <div class="feature">
            <div class="feature-icon"><i class="fas fa-bell bell-ring"></i></div>
            <div class="feature-name">Notificaciones</div>
            <div class="feature-desc">Alertas por correo electrónico y notificaciones en la plataforma para cada asignación, corrección o cierre.</div>
        </div>
        <div class="feature">
            <div class="feature-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="feature-name">SLA visible</div>
            <div class="feature-desc">Indicador visual en la bandeja de entrada cuando una etapa excede su tiempo objetivo. Es netamente informativo y no interrumpe el flujo.</div>
        </div>
        <div class="feature">
            <div class="feature-icon"><i class="fas fa-paperclip"></i></div>
            <div class="feature-name">Adjuntos por revisión</div>
            <div class="feature-desc">Los documentos se agrupan según el número de revisión. Los archivos de versiones anteriores permanecen disponibles para consulta.</div>
        </div>
        <div class="feature">
            <div class="feature-icon"><i class="fas fa-stream"></i></div>
            <div class="feature-name">Línea de tiempo</div>
            <div class="feature-desc">Cada acción queda registrada con el actor, la fecha y sus respectivos comentarios. El historial de auditoría es inmutable.</div>
        </div>
        <div class="feature">
            <div class="feature-icon"><i class="fas fa-user-shield"></i></div>
            <div class="feature-name">Roles Spatie</div>
            <div class="feature-desc">Se integra de forma nativa con el esquema de permisos existente, evitando la duplicidad en la gestión de accesos.</div>
        </div>
        <div class="feature">
            <div class="feature-icon"><i class="fas fa-life-ring"></i></div>
            <div class="feature-name">Opciones de control</div>
            <div class="feature-desc">Los administradores pueden reabrir, reasignar o cancelar cualquier flujo sin alterar la integridad del expediente.</div>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════════ FAQ ═══ -->
<section class="chapter reveal" id="preguntas">
    <div class="chapter-head">
        <div class="chapter-num">FAQ<br>8&nbsp;dudas</div>
        <div>
            <h2 class="chapter-title">Preguntas frecuentes</h2>
            <p class="chapter-sub">Las dudas más comunes de quienes usan el sistema por primera vez. Si tenés alguna que no esté acá, avisale a tu administrador para agregarla.</p>
        </div>
    </div>

    <div class="faq" x-data="{ open: 0 }">
        <template x-for="(item, i) in faqs" :key="i">
            <div class="faq-item" :class="{ open: open === i }">
                <button class="faq-q" @click="open = open === i ? null : i">
                    <span x-text="item.q"></span>
                    <i class="fas fa-plus faq-toggle"></i>
                </button>
                <div class="faq-a" x-show="open === i" x-collapse>
                    <p x-html="item.a"></p>
                </div>
            </div>
        </template>
    </div>
</section>

</div>

<footer>
    <div class="wrap footer-row">
        <div>MÓDULO/FLUJOS &nbsp;&nbsp;·&nbsp;&nbsp; OFICANON &nbsp;&nbsp;·&nbsp;&nbsp; CÚCUTA, CO</div>
        <div>BUILD 2026.07 &nbsp;&nbsp;·&nbsp;&nbsp; SOFTWARE &nbsp;&nbsp;·&nbsp;&nbsp; SPATIE 6</div>
    </div>
</footer>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('motor', () => ({
        codigo: 'FLJ-2026-000127',
        revision: 1,
        estado: 'en_progreso',
        etapaActual: 0,
        etapas: [],
        eventos: [],
        eventoId: 0,
        autoplay: true,
        simulandoRechazo: false,
        intervalRef: null,
        contadorPausa: 0,

        plantilla: [
            { nombre: 'Revisión Técnica',       modoLabel: 'SECUENCIAL',         modo: 'secuencial',      actores: [ { nombre: 'María Torres', iniciales: 'MT' } ] },
            { nombre: 'Aprobación Financiera',  modoLabel: 'PARALELO · TODOS',   modo: 'paralelo_todos',  actores: [ { nombre: 'Ana Gómez', iniciales: 'AG' }, { nombre: 'Roberto Mora', iniciales: 'RM' } ] },
            { nombre: 'Firma Final',            modoLabel: 'SECUENCIAL',         modo: 'secuencial',      actores: [ { nombre: 'Dr. Oscar', iniciales: 'DP' } ] }
        ],

        init() {
            this.reiniciar();
            this.intervalRef = setInterval(() => this.tick(), 1400);
        },

        cerrado() { return ['aprobado', 'cancelado'].includes(this.estado); },
        etiquetaEstado() {
            return { 'en_progreso': 'En progreso', 'requiere_correccion': 'Requiere corrección',
                     'aprobado': 'Aprobado', 'cancelado': 'Cancelado' }[this.estado] || this.estado;
        },
        formatearHora() { return new Date().toTimeString().substring(0, 8); },
        log(msg) {
            this.eventos.push({ id: ++this.eventoId, time: this.formatearHora(), msg });
            if (this.eventos.length > 20) this.eventos.shift();
        },

        reiniciar() {
            this.codigo = 'FLJ-2026-' + String(127 + Math.floor(Math.random()*40)).padStart(6, '0');
            this.revision = 1;
            this.estado = 'en_progreso';
            this.etapaActual = 0;
            this.simulandoRechazo = false;
            this.autoplay = true;
            this.contadorPausa = 0;
            this.etapas = this.plantilla.map(e => ({
                ...e,
                rechazada: false,
                actores: e.actores.map(a => ({
                    ...a,
                    estado: 'pendiente',
                    estadoLabel() { return { pendiente: '···', aprobado: 'FIRMADO', rechazado: 'RECHAZÓ', superseded: '—' }[this.estado]; }
                }))
            }));
            this.eventos = [];
            this.log(`Trámite iniciado por Juan Rodríguez · Rev. 1`);
        },

        pausar() { this.autoplay = !this.autoplay; },

        rechazar() {
            if (this.cerrado()) return;
            this.simulandoRechazo = true;
            const etapa = this.etapas[this.etapaActual];
            const actor = etapa.actores.find(a => a.estado === 'pendiente');
            if (!actor) return;
            actor.estado = 'rechazado';
            etapa.rechazada = true;
            etapa.actores.forEach(a => { if (a.estado === 'pendiente') a.estado = 'superseded'; });
            this.log(`${actor.nombre} rechazó "${etapa.nombre}" — política: devolver al iniciador`);
            setTimeout(() => {
                this.estado = 'requiere_correccion';
                this.log(`El iniciador recibió notificación de corrección`);
                setTimeout(() => {
                    this.log(`Iniciador corrigió y reenvió — Rev. ${this.revision + 1}`);
                    this.revision++;
                    this.estado = 'en_progreso';
                    this.etapaActual = 0;
                    this.simulandoRechazo = false;
                    this.etapas.forEach(e => { e.rechazada = false; e.actores.forEach(a => a.estado = 'pendiente'); });
                }, 2200);
            }, 1500);
        },

        tick() {
            if (!this.autoplay || this.simulandoRechazo) return;
            if (this.cerrado()) {
                this.contadorPausa++;
                if (this.contadorPausa > 3) this.reiniciar();
                return;
            }
            const etapa = this.etapas[this.etapaActual];
            const pendiente = etapa.actores.find(a => a.estado === 'pendiente');
            if (pendiente) {
                pendiente.estado = 'aprobado';
                this.log(`${pendiente.nombre} aprobó "${etapa.nombre}"`);
                const modoAvance = etapa.modo === 'paralelo_cualquiera'
                    ? etapa.actores.some(a => a.estado === 'aprobado')
                    : etapa.actores.every(a => a.estado === 'aprobado');
                if (modoAvance) {
                    if (this.etapaActual + 1 < this.etapas.length) {
                        this.etapaActual++;
                        setTimeout(() => this.log(`Motor avanzó a "${this.etapas[this.etapaActual].nombre}"`), 400);
                    } else {
                        this.estado = 'aprobado';
                        this.log(`Flujo cerrado como APROBADO en todas sus etapas`);
                    }
                }
            }
        },
    }));
});

const faqData = [
    { q: '¿Puedo iniciar un flujo si soy usuario común?',
      a: 'Sí. Cualquiera con el permiso <code>flujos.iniciar</code> puede arrancar un trámite. Solo necesitás que exista al menos un <b>tipo de documento</b> con plantilla activa en el sistema.' },
    { q: '¿Cómo sé que me tocó aprobar algo?',
      a: 'Te llega un correo y aparece un badge rojo en la campana del navbar. Además, la pestaña <em>Mis Pendientes</em> de tu bandeja muestra un contador con todo lo que te espera.' },
    { q: '¿Qué pasa si rechazo una etapa?',
      a: 'Depende de la <b>política de rechazo</b> que le puso el diseñador a esa etapa: puede volver al iniciador para que corrija, retroceder una etapa, saltar a una etapa específica, o cerrar el flujo. Nunca es implícito — siempre está declarado.' },
    { q: '¿Puedo recuperar un flujo que se cerró por error?',
      a: 'Solo un administrador puede reabrir un flujo cancelado. Cuando lo hace, el flujo retoma desde la última etapa donde estaba activo, con asignaciones frescas.' },
    { q: '¿Los documentos que subo se pierden al corregir?',
      a: 'No. Cada archivo queda etiquetado con la <b>revisión</b> en la que fue subido. Los adjuntos de revisiones anteriores siguen visibles y descargables como historial. El expediente crece con el tiempo, nunca se sobrescribe.' },
    { q: '¿Puedo editar el trámite después de iniciarlo?',
      a: 'No en su estructura (título, tipo, plantilla). Pero podés <b>agregar más adjuntos</b> mientras esté en progreso, y si te devuelven para corregir, tenés la oportunidad de reenviar una versión ajustada.' },
    { q: '¿Los aprobadores ven quién más aprobó antes?',
      a: 'Sí. La <em>línea de tiempo</em> del detalle del flujo muestra cada decisión con quién la tomó, cuándo y con qué comentario. La transparencia es parte del diseño.' },
    { q: '¿Qué es el SLA que aparece en algunas etapas?',
      a: 'Es el <b>tiempo objetivo</b> para resolver una etapa (en horas). Es informativo — no cancela nada por sí solo. Solo activa un badge visual en la bandeja cuando la etapa lleva más tiempo del previsto.' },
];

document.addEventListener('alpine:init', () => {
    Alpine.store('faqs', faqData);
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[x-data*="open: 0"]').forEach(el => {
        el.setAttribute('x-data', `{ open: 0, faqs: ${JSON.stringify(faqData)} }`);
    });
});

if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.15 });
    document.querySelectorAll('.reveal').forEach(el => io.observe(el));
}
</script>

</body>
</html>