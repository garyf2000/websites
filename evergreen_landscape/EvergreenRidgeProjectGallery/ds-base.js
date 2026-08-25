// Loads this design system into the template.
// Auto-detects whether this file is running inside the design system's own
// project (base = '../..') or inside a project that has BOUND this design
// system under a `_ds/<folder>` directory — no manual edit required in the
// common case. If detection fails, it shows a visible on-page banner (not a
// silent console-only failure) telling whoever is looking exactly what to fix.
(() => {
  // This design system's bound folder name is fixed (derived from this DS
  // project itself), so it's the same in every consuming project regardless
  // of where in that project's file tree this template was placed.
  const DS_FOLDER = 'extreme-networks-react-design-system-113cf159-3ec7-42ad-a526-98c370bc9ed4';
  const candidates = [
    '../..', // previewing inside this design-system project itself
    '_ds/' + DS_FOLDER, // consuming project, this file at project root
    '../_ds/' + DS_FOLDER, // consuming project, this file one level deep
    '../../_ds/' + DS_FOLDER, // consuming project, this file two levels deep
    '../../../_ds/' + DS_FOLDER,
  ];

  const tryBase = (base) => fetch(base + '/_ds_bundle.js', { method: 'HEAD' })
    .then((r) => r.ok ? base : Promise.reject())
    .catch(() => fetch(base + '/_ds_bundle.js')
      .then((r) => r.ok ? base : Promise.reject()));

  const findBase = async () => {
    for (const c of candidates) {
      try { return await tryBase(c); } catch (e) {}
    }
    // Last resort: if '_ds' exists at the project root but has a nested
    // folder we can't guess the name of, probe one level via a manifest hint
    // some bindings drop at the root.
    try {
      const res = await fetch('../../_ds/manifest.json');
      if (res.ok) {
        const j = await res.json();
        if (j && j.folder) return '../../_ds/' + j.folder;
      }
    } catch (e) {}
    return null;
  };

  const showError = () => {
    const el = document.createElement('div');
    el.style.cssText = 'position:fixed;inset:0;z-index:99999;display:flex;align-items:center;justify-content:center;background:#1a1a1a;color:#fff;font-family:system-ui,sans-serif;padding:32px;box-sizing:border-box';
    el.innerHTML = '<div style="max-width:560px;text-align:left"><div style="font-size:18px;font-weight:600;margin-bottom:12px">Design system not connected</div><div style="font-size:14px;line-height:1.6;opacity:0.85">This template could not find the bound design system, so components are showing as empty placeholders. In this project, open <code style="background:#333;padding:2px 6px;border-radius:4px">templates/app-shell/ds-base.js</code> and set the <code style="background:#333;padding:2px 6px;border-radius:4px">candidates</code> list at the top to the path where the design system was bound (check the project file list for a <code style="background:#333;padding:2px 6px;border-radius:4px">_ds/&lt;folder&gt;</code> directory).</div></div>';
    document.body.appendChild(el);
  };

  const loadScript = (src) => new Promise((resolve, reject) => {
    const s = document.createElement('script');
    s.src = src; s.onload = resolve; s.onerror = reject;
    document.head.appendChild(s);
  });

  // If the real vendored ag-grid isn't reachable (e.g. a consuming project's
  // bound _ds/<folder> copy excludes _vendor/), install a no-op stub so
  // _ds_bundle.js's ag-grid shim doesn't throw at load time and take down
  // every other component with it. ENDataGrid itself just won't function.
  const installAgGridStub = () => {
    if (window.__dsAgGrid) return;
    const stub = new Proxy(function () {}, { get: () => stub, apply: () => stub, construct: () => stub });
    window.__dsAgGrid = new Proxy({}, { has: () => true, get: () => stub });
  };

  // If a consuming project's bound _ds/<folder> copy dropped _vendor/ag-grid.js
  // (a common effect of size-capped project binding), try loading the same
  // ag-grid version from a CDN before giving up and installing the no-op stub.
  const AG_VERSION = '35.3.0';
  const tryAgGridCdn = () => {
    const c1 = 'https://cdn.jsdelivr.net/npm/ag-grid-community@' + AG_VERSION + '/dist/ag-grid-community.min.js';
    const c2 = 'https://cdn.jsdelivr.net/npm/ag-grid-enterprise@' + AG_VERSION + '/dist/ag-grid-enterprise.min.js';
    return loadScript(c1).then(() => loadScript(c2)).then(() => {
      const g = window.agGrid;
      const ok = g && g.ModuleRegistry && g.createGrid && g.provideGlobalGridOptions && g.AllEnterpriseModule && g.LicenseManager;
      if (ok) window.__dsAgGrid = g;
      return !!ok;
    }).catch(() => false);
  };

  findBase().then((base) => {
    if (!base) { showError(); return; }
    for (const p of ['_ds_bundle.css', 'styles.css']) {
      const l = document.createElement('link');
      l.rel = 'stylesheet'; l.href = base + '/' + p;
      document.head.appendChild(l);
    }
    loadScript(base + '/_vendor/ag-grid.js')
      .catch(() => tryAgGridCdn().then((ok) => { if (!ok) installAgGridStub(); }))
      .then(() => { loadScript(base + '/_ds_bundle.js').catch(showError); });
  });
})();
