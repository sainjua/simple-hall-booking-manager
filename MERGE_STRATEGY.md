# Merge Strategy Guide

This guide explains how to safely merge changes between branches while keeping `main` clean.

## 🔒 Protected Files in Main Branch

The following files/folders are **automatically ignored** in `main` branch via `.gitignore`:

### Development Documentation (from `dev` branch)
- `aidocument/` - All development documentation

### Test Files (from `test` branch)
- `QUICK_TEST_CHECKLIST.txt`
- `TESTING_README.md`
- `TESTING_SUMMARY.txt`
- `TEST_CASES.txt`
- `TEST_PLAN.md`
- `automation-tests-puppeteer.js`
- `automation-tests.js`
- `package.json`
- `package-lock.json`
- `test-results.json`
- `test-screenshots/`
- `node_modules/`

## ✅ Safe Merge Workflows

### Merging Production Code from `dev` to `main`

```bash
# 1. Switch to main branch
git checkout main

# 2. Merge from dev (only tracked files will merge)
git merge dev

# 3. Verify no unwanted files were added
git status

# 4. The .gitignore will prevent aidocument/ from being tracked
# Even if merge tries to add it, git will ignore it
```

### Merging Production Code from `test` to `main`

```bash
# 1. Switch to main branch
git checkout main

# 2. Merge from test (only tracked files will merge)
git merge test

# 3. Verify no test files were added
git status

# 4. The .gitignore will prevent test files from being tracked
```

## 🛡️ How Protection Works

1. **`.gitignore` in `main` branch** explicitly lists all files to ignore
2. When you merge from `dev` or `test`, Git will:
   - ✅ Merge tracked files (plugin code)
   - ❌ Ignore files listed in `.gitignore`
   - ⚠️ Show untracked files in `git status` but won't commit them

3. **Result:** Only production code gets merged, never test files or dev docs

## 📝 Best Practices

### Before Merging to Main

1. **Review changes:**
   ```bash
   git diff main..dev
   ```

2. **Check what will be merged:**
   ```bash
   git log main..dev --oneline
   ```

3. **Perform the merge:**
   ```bash
   git checkout main
   git merge dev --no-ff -m "Merge feature X from dev"
   ```

4. **Verify clean status:**
   ```bash
   git status
   ls -la | grep -E "(aidocument|test|package)"
   ```
   Should return nothing or show as untracked

### After Merging

1. **Verify main is clean:**
   ```bash
   git checkout main
   ls -la
   ```
   Should NOT contain:
   - `aidocument/`
   - Test files
   - `node_modules/`

2. **Push to remote:**
   ```bash
   git push origin main
   ```

## 🚨 Emergency: If Unwanted Files Get Merged

If somehow test files or aidocument accidentally get committed to main:

```bash
# Remove from git but keep locally
git rm -r --cached aidocument/
git rm --cached automation-tests*.js package*.json

# Commit the removal
git commit -m "Remove dev/test files from main branch"

# Verify .gitignore is working
git status
```

## 🔄 Recommended Workflow

```
dev branch (development docs)
  ↓
  → Merge production code only → main (clean for WordPress)
  
test branch (test files)
  ↓
  → Merge production code only → main (clean for WordPress)
```

## ✨ Summary

- **`.gitignore` protects `main` automatically**
- **Merging is safe** - ignored files won't be tracked
- **Always verify** with `git status` after merge
- **Main stays clean** for WordPress submission

---

**Remember:** The `.gitignore` file in `main` branch is your safety net. As long as it's properly configured, you can safely merge from `dev` or `test` without worrying about polluting the main branch.
