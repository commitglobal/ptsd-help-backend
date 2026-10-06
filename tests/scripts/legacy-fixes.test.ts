import { applyLegacyFixes } from '../../scripts/lib/legacy-fixes';

const topicWith = (sections: unknown[]) => ({
  title: 'T',
  pages: [
    { id: 'c1', type: 'category', label: 'C', icon: 'i', topics: [{ id: 't1', label: 'L', icon: 'i', content: { sections } }] },
    { id: 't2', type: 'topic', label: 'L2', icon: 'i', content: { sections } },
  ],
});

const firstSections = (json: any) => json.pages[0].topics[0].content.sections;
const topicSections = (json: any) => json.pages[1].content.sections;

describe('applyLegacyFixes', () => {
  it('prefixes https:// to a scheme-less external url', () => {
    const { fixed, warnings } = applyLegacyFixes(
      topicWith([{ type: 'button', label: 'Go', action: { type: 'external', url: 'example.am/' } }])
    );
    expect(firstSections(fixed)[0].action.url).toBe('https://example.am/');
    expect(topicSections(fixed)[0].action.url).toBe('https://example.am/');
    expect(warnings).toContain('WARN: external url without scheme "example.am/" imported as "https://example.am/"');
  });

  it('prefixes https:// to a scheme-less webview url', () => {
    const { fixed } = applyLegacyFixes(
      topicWith([{ type: 'button', label: 'Go', action: { type: 'webview', url: 'example.ro/page' } }])
    );
    expect(firstSections(fixed)[0].action.url).toBe('https://example.ro/page');
  });

  it('leaves http, https and mailto urls untouched', () => {
    const sections = [
      { type: 'button', label: 'A', action: { type: 'external', url: 'http://a.am' } },
      { type: 'button', label: 'B', action: { type: 'webview', url: 'https://b.am/x' } },
      { type: 'button', label: 'C', action: { type: 'external', url: 'mailto:c@c.am' } },
    ];
    const { fixed, warnings } = applyLegacyFixes(topicWith(sections));
    expect(firstSections(fixed)).toEqual(sections);
    expect(warnings).toEqual([]);
  });

  it('replaces empty and whitespace-only button labels in sections', () => {
    const { fixed, warnings } = applyLegacyFixes(
      topicWith([
        { type: 'button', label: '', action: { type: 'external', url: 'https://deprehub.ro/psihoterapie-gratuita' } },
        { type: 'button', label: '   ', action: { type: 'phone', number: '112' } },
      ])
    );
    expect(firstSections(fixed)[0].label).toBe('deprehub.ro/psihoterapie-gratuita');
    expect(firstSections(fixed)[1].label).toBe('112');
    expect(warnings).toContain(
      'WARN: button without label (action {"type":"external","url":"https://deprehub.ro/psihoterapie-gratuita"}) imported with label "deprehub.ro/psihoterapie-gratuita"'
    );
  });

  it('replaces blank button labels inside multiContent and multiPage blocks', () => {
    const { fixed } = applyLegacyFixes(
      topicWith([
        { type: 'multiContent', contentArray: [{ type: 'button', label: '', action: { type: 'webview', url: 'x.am/a' } }] },
        { type: 'multiPage', pageArray: [[{ type: 'button', label: ' ', action: { type: 'external', url: 'https://y.am/' } }]] },
      ])
    );
    const [multiContent, multiPage] = firstSections(fixed);
    expect(multiContent.contentArray[0]).toEqual({ type: 'button', label: 'x.am/a', action: { type: 'webview', url: 'https://x.am/a' } });
    expect(multiPage.pageArray[0][0].label).toBe('y.am');
  });

  it('leaves non-button content untouched', () => {
    const sections = [
      { type: 'image', src: 'https://s3/a.png', atl: 'A' },
      { type: 'rich-text', content: '<p>x.am</p>' },
      { type: 'contact', label: '' },
      { type: 'button', label: 'Ok', action: { type: 'start_tool', toolId: 'grounding' } },
    ];
    const input = topicWith(sections);
    const { fixed, warnings } = applyLegacyFixes(input);
    expect(fixed).toEqual(input);
    expect(warnings).toEqual([]);
  });

  it('does not mutate the input', () => {
    const input = topicWith([{ type: 'button', label: '', action: { type: 'external', url: 'z.am' } }]);
    const snapshot = JSON.parse(JSON.stringify(input));
    const { fixed } = applyLegacyFixes(input);
    expect(input).toEqual(snapshot);
    expect(fixed).not.toBe(input);
  });
});
