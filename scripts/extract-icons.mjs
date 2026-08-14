import { mkdirSync, writeFileSync, readFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const iconsDir = join(__dirname, '..', 'node_modules', 'lucide', 'dist', 'esm', 'icons');
const outDir = join(__dirname, '..', 'resources', 'views', 'components', 'icon');
mkdirSync(outDir, { recursive: true });

// Curated set of Lucide icon names used across the platform.
// Add more names here and re-run `node scripts/extract-icons.mjs` any time a view needs a new one.
const ICON_NAMES = [
    'layout-dashboard', 'users', 'user', 'user-check', 'user-plus', 'user-x',
    'calendar', 'calendar-days', 'calendar-check', 'briefcase', 'message-square', 'message-circle',
    'bell', 'settings', 'search', 'menu', 'x', 'heart', 'map-pin', 'graduation-cap', 'building',
    'building-2', 'landmark', 'dollar-sign', 'chart-bar', 'chart-bar-big', 'chart-column', 'chart-pie',
    'trending-up', 'shield', 'shield-check', 'file-text', 'mail', 'phone', 'globe', 'link',
    'link-2', 'lock', 'log-out', 'log-in', 'chevron-down', 'chevron-right', 'chevron-left',
    'chevron-up', 'chevrons-right', 'plus', 'minus', 'check', 'circle-check', 'circle-x',
    'circle-alert', 'triangle-alert', 'info', 'pencil', 'trash', 'trash-2', 'download', 'upload',
    'filter', 'sliders-horizontal', 'ellipsis', 'ellipsis-vertical', 'eye', 'eye-off', 'star',
    'award', 'book-open', 'book', 'newspaper', 'megaphone', 'gift', 'handshake', 'house',
    'arrow-right', 'arrow-left', 'arrow-up-right', 'arrow-up', 'arrow-down', 'external-link',
    'clock', 'map', 'flag', 'thumbs-up', 'share-2', 'send', 'paperclip', 'image', 'camera',
    'credit-card', 'wallet', 'target', 'layers', 'list', 'grid-3x3', 'table', 'folder',
    'folder-open', 'archive', 'ban', 'lock-open', 'key', 'sun', 'moon', 'palette', 'activity',
    'database', 'server', 'clipboard-list', 'clipboard-check', 'badge-check', 'rocket', 'sparkles',
    'network', 'video', 'mic', 'bookmark', 'tag', 'tags', 'hash', 'smartphone', 'laptop',
    'printer', 'package', 'shopping-bag', 'ticket', 'trophy', 'medal', 'compass', 'navigation',
    'refresh-cw', 'loader-circle', 'circle-dot', 'panel-left', 'layout-grid', 'layout-list', 'columns-3',
    'monitor', 'cloud-upload', 'file', 'file-plus', 'file-check', 'file-x', 'files', 'folder-plus',
    'inbox', 'contact', 'library', 'school', 'banknote', 'receipt', 'percent', 'calculator',
    'scale', 'gavel', 'map-pinned', 'languages', 'command', 'sparkle', 'zap', 'sunrise', 'flame',
    'crown', 'gauge', 'circle-check-big', 'circle-help',
    'square-pen', 'square-check', 'toggle-left', 'toggle-right', 'wifi', 'wifi-off',
    'volume-2', 'play', 'pause', 'circle-stop', 'skip-forward', 'copy', 'clipboard', 'save',
    'undo-2', 'redo-2', 'move', 'maximize-2', 'minimize-2', 'expand', 'shrink', 'lightbulb',
];

const camelToKebab = (s) => s.replace(/([a-z0-9])([A-Z])/g, '$1-$2').toLowerCase();

let ok = 0, fail = 0;

for (const name of ICON_NAMES) {
    try {
        const src = readFileSync(join(iconsDir, `${name}.js`), 'utf8');
        const tagPattern = /\[\s*"([a-zA-Z]+)"\s*,\s*\{([^}]*)\}\s*\]/g;
        const elements = [];
        let m;
        while ((m = tagPattern.exec(src)) !== null) {
            const [, tag, rawAttrs] = m;
            const attrPattern = /"?([\w-]+)"?\s*:\s*"([^"]*)"/g;
            const attrs = [];
            let am;
            while ((am = attrPattern.exec(rawAttrs)) !== null) {
                attrs.push(`${camelToKebab(am[1])}="${am[2]}"`);
            }
            elements.push(`<${tag} ${attrs.join(' ')} />`);
        }

        if (elements.length === 0) {
            throw new Error('no child elements matched');
        }

        const blade = `@props(['class' => 'w-5 h-5'])
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" {{ $attributes->merge(['class' => $class]) }}>
    ${elements.join('\n    ')}
</svg>
`;
        writeFileSync(join(outDir, `${name}.blade.php`), blade);
        ok++;
    } catch (e) {
        console.error(`FAILED: ${name} - ${e.message}`);
        fail++;
    }
}

console.log(`Generated ${ok} icon components, ${fail} failed.`);
