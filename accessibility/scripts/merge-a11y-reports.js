import fs from "fs";
import path from "path";

// Path Configuration
const CONFIG = {
  paths: {
    pa11y: "reports/pa11y/pa11y-summary.json",
    htmlvalidate: "reports/htmlvalidate/htmlvalidate-report.json",
    output: "reports/full-a11y-reports/full-a11y-report.json"
  }
};

//Creates unique Issue Keys
function createIssueKey(issue) {
    return `${issue.code}-${issue.selector || issue.line || "?"}-${issue.url || issue.file || "?"}`;
}

//Normalizes Data Structure for each Tool
const normalizers = {
  pa11y: (data) => {
    const results = [];
    for(const [url, issues] of Object.entries(data.results || {})) {
      issues.forEach(i => results.push({
        tool: "pa11y",
        url,
        code: i.code,
        message: i.message,
        selector: i.selector || null,
        context: i.context || null,
        wcagRef: i.runnerExtras?.helpUrl || null,
        impact: i.runnerExtras?.impact || null
      }));
    }
    return results;
  },
  htmlvalidate: (data) => {
    const files = Array.isArray(data) ? data : data.files || data.results || [];
    return files.flatMap(file =>
      (file.messages || []).map(m => ({
        tool: "html-validate",
        file: file.filePath,
        code: m.ruleId,
        message: m.message,
        line: m.line,
        selector: m.selector || `line:${m.line}`,
        wcagRef: m.ruleUrl?.includes("wcag") ? m.ruleUrl : null,
        impact: m.severity === 2 ? "error" : "warning"
      }))
    );
  }
};

// Merges Reports and deduplicates Issues
function deduplicateIssues(issueArrays) {
  const allIssues = issueArrays.flat();
  const uniqueMap = new Map();

  for (const issue of allIssues) {
    const key = createIssueKey(issue);

    if (!uniqueMap.has(key)) {
      uniqueMap.set(key, issue);
    } else {
      const existing = uniqueMap.get(key);
      if (!existing.tool.includes(issue.tool)) {
        existing.tool += `, ${issue.tool}`;
      }
    }
  }

  return Array.from(uniqueMap.values());
}

function loadJSON(filePath) {
  try {
    return JSON.parse(fs.readFileSync(filePath, "utf-8"));
  } catch (err) {
    throw new Error(`Failed to load ${filePath}: ${err.message}`);
  }
}

// Main function
function mergeA11yReports() {
  try {
    const pa11yData = loadJSON(CONFIG.paths.pa11y);
    const htmlData = loadJSON(CONFIG.paths.htmlvalidate);

    const pa11yIssues = normalizers.pa11y(pa11yData);
    const htmlIssues = normalizers.htmlvalidate(htmlData);

    const mergedIssues = deduplicateIssues([pa11yIssues, htmlIssues]);

    const report = {
      total: mergedIssues.length,
      tools: {
        pa11y: pa11yIssues.length,
        htmlvalidate: htmlIssues.length
      },
      issues: mergedIssues
    };

    // Output
    const outputDir = path.dirname(CONFIG.paths.output);
    fs.mkdirSync(outputDir, { recursive: true });
    fs.writeFileSync(CONFIG.paths.output, JSON.stringify(report, null, 2));

    console.log(`Combined report written: ${CONFIG.paths.output}`);
    console.log(`Total unique issues: ${mergedIssues.length}`);
    console.log(`Breakdown: Pa11y = ${pa11yIssues.length}, html-validate = ${htmlIssues.length}`);

  } catch (err) {
    console.error("Error merging reports:", err.message);
    process.exit(1);
  }
}

mergeA11yReports();

