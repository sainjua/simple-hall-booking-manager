# Branch Structure

This repository uses three branches to keep the WordPress submission clean:

## 🌿 `main` Branch (WordPress Submission)
**Purpose:** Clean, production-ready code for WordPress.org submission

**Contains:**
- All plugin source code
- Documentation (README.txt, readme files)
- Assets for WordPress.org

**Does NOT contain:**
- Test files
- Automation scripts
- Development documentation (aidocument/)
- Development tools
- node_modules

**Use this branch for:**
- Creating WordPress plugin ZIP files
- Submitting to WordPress.org Plugin Directory
- Production deployments

## 🧪 `test` Branch (Testing & Automation)
**Purpose:** Testing branch with all testing tools and automation

**Contains:**
- Everything from `main` branch
- Test automation scripts (Puppeteer, etc.)
- Test documentation and checklists
- Test cases and test plans
- package.json and dependencies
- Test results and screenshots

**Use this branch for:**
- Running automated tests
- Adding new test cases
- Reviewing test results
- Quality assurance

## 🔧 `dev` Branch (Development Documentation)
**Purpose:** Development branch with technical documentation

**Contains:**
- Everything from `main` branch
- Development documentation (aidocument/)
- Architecture diagrams
- Implementation guides
- Bug fix documentation
- Feature implementation notes

**Use this branch for:**
- Reviewing development history
- Understanding implementation details
- Planning new features
- Reference documentation

## Switching Between Branches

```bash
# Switch to main (for WordPress submission)
git checkout main

# Switch to test (for testing)
git checkout test

# Switch to dev (for development documentation)
git checkout dev

# View current branch
git branch
```

## Workflow

1. **Development:** Work on features, keep docs in `dev` branch
2. **Testing:** Run all tests in `test` branch
3. **Merge to main:** Once tested, merge production code to `main`
4. **WordPress Submission:** Use `main` branch to create plugin ZIP

## Important Notes

- Test files and aidocument are automatically ignored in `main` branch via `.gitignore`
- Always test in `test` branch before merging to `main`
- Keep development documentation in `dev` branch
- Keep `main` branch clean and ready for WordPress submission at all times
