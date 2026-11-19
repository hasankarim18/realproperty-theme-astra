# Taste

- Expects larger changes to be analyzed and planned first, done on its own feature branch with a namespaced name (e.g. `sp/image-modal`), and committed once finished. Confidence: 0.6
- Keeps a fixed WordPress theme asset layout: page-specific CSS appended to the existing stylesheet in `inc/property/css/`, page JS as its own file in `inc/property/js/`, and scripts enqueued through the existing hooks in `loadPropertyAssets.php` rather than new registration points. Confidence: 0.6
- Prefers menu/nav open-close and dropdowns animated with simple CSS (fade/slide) rather than instant `display: none` toggles, and wants nested menus (child and grandchild) to use accordion/collapse behavior on tablet and mobile. Confidence: 0.7
- For distinct responsive breakpoints, prefers dedicated/separate markup (e.g. a separate mobile menu) shown/hidden via CSS media queries rather than one component adapting across ranges, and expects layouts to work down to ~350px narrow phones. Confidence: 0.6
