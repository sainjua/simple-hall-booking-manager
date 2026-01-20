# Branch Structure

This repository uses two branches to keep the WordPress submission clean:

## 🌿 `main` Branch (WordPress Submission)
**Purpose:** Clean, production-ready code for WordPress.org submission

**Contains:**
- All plugin source code
- Documentation (README.txt, readme files)
- Assets for WordPress.org

**Does NOT contain:**
- Test files
- Automation scripts
- Development tools
- node_modules

**Use this branch for:**
- Creating WordPress plugin ZIP files
- Submitting to WordPress.org Plugin Directory
- Production deployments

## 🧪 `test` Branch (Development & Testing)
**Purpose:** Development branch with all testing tools and automation

**Contains:**
- Everything from `main` branch
- Test automation scripts (Puppeteer, etc.)
- Test documentation and checklists
- Test cases and test plans
- package.json and dependencies
- Test results and screenshots

**Use this branch for:**
- Running automated tests
- Development and testing
- Adding new test cases
- Reviewing test results

## Switching Between Branches

```bash
# Switch to main (for WordPress submission)
git checkout main

# Switch to test (for development/testing)
git checkout test

# View current branch
git branch
```

## Workflow

1. **Development:** Work on features in `test` branch
2. **Testing:** Run all tests in `test` branch
3. **Merge to main:** Once tested, merge production code to `main`
4. **WordPress Submission:** Use `main` branch to create plugin ZIP

## Important Notes

- Test files are automatically ignored in `main` branch via `.gitignore`
- Always test in `test` branch before merging to `main`
- Keep `main` branch clean and ready for WordPress submission at all times
