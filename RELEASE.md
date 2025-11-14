# Release Process

This document describes how to create a new release of the Matomo MediaWiki Extension.

## Overview

The release process is automated using GitHub Actions. When a new version tag is pushed, the workflow automatically:

1. Builds the extension with production dependencies
2. Creates a distribution package (zip file)
3. Generates checksums for verification
4. Creates a GitHub release with release notes
5. Attaches the distribution package and checksums to the release

## Prerequisites

Before creating a release, ensure:

- [ ] All changes are merged to the `master` branch
- [ ] All tests pass on the `master` branch
- [ ] The version number in `extension.json` and `README.md` is updated
- [ ] You have reviewed the changes since the last release

## Creating a Release

### Step 1: Update Version Numbers

Update the version number in the following files:

1. **extension.json** - Update the `"version"` field:
   ```json
   {
     "name": "Matomo",
     "version": "5.0.1",
     ...
   }
   ```

2. **README.md** - Update the version at the top:
   ```markdown
   Version 5.0.1
    - Last update: DD Month YYYY
   ```

### Step 2: Commit Version Updates

```bash
git add extension.json README.md
git commit -m "Bump version to 5.0.1"
git push origin master
```

### Step 3: Create and Push a Version Tag

Create a Git tag following semantic versioning (e.g., v5.0.1):

```bash
# Create an annotated tag
git tag -a v5.0.1 -m "Release version 5.0.1"

# Push the tag to GitHub
git push origin v5.0.1
```

### Step 4: Monitor the Release Workflow

1. Go to the [Actions tab](../../actions) in the GitHub repository
2. Watch the "Release" workflow execution
3. The workflow typically completes in 2-3 minutes

### Step 5: Verify the Release

Once the workflow completes:

1. Go to the [Releases page](../../releases)
2. Verify that the new release is created
3. Check that the following files are attached:
   - `matomo-mediawiki-extension-vX.Y.Z.zip`
   - `matomo-mediawiki-extension-vX.Y.Z.zip.sha256`
4. Review the auto-generated release notes

### Step 6: Edit Release Notes (Optional)

You may want to manually edit the release notes to:

- Add a summary of major changes
- Highlight breaking changes or important updates
- Include migration instructions if needed
- Add acknowledgments to contributors

## Release Workflow Details

### Workflow Trigger

The release workflow is triggered automatically when a tag matching the pattern `v*` is pushed:

```yaml
on:
  push:
    tags:
      - 'v*'
```

### What Gets Included in the Release Package

The distribution package includes:

- `extension.json` - Extension metadata and configuration
- `README.md` - Documentation
- `src/` - PHP source code
- `vendor/` - Production dependencies (autoloader only)

The following are **excluded** from the release package:

- Development dependencies (phpcs, testing tools)
- `.git` directory and `.gitignore`
- `.github` workflows
- `composer.json` and `composer.lock`
- `.phpcs.xml` configuration
- `Rakefile`

### Workflow Permissions

The workflow requires `contents: write` permission to:

- Create releases
- Upload release assets

This permission is automatically granted by GitHub Actions.

## Versioning Scheme

This project follows [Semantic Versioning](https://semver.org/):

- **MAJOR** version (X.0.0): Incompatible changes, major new features
- **MINOR** version (X.Y.0): New features, backwards compatible
- **PATCH** version (X.Y.Z): Bug fixes, backwards compatible

Examples:
- `v5.0.0` - Major release with breaking changes
- `v5.1.0` - Minor release with new features
- `v5.0.1` - Patch release with bug fixes

## Troubleshooting

### Workflow Fails to Create Release

**Problem**: The workflow runs but fails to create the release.

**Solution**: 
1. Check the Actions log for error messages
2. Verify that the repository has "Read and write permissions" enabled:
   - Go to Settings → Actions → General
   - Under "Workflow permissions", select "Read and write permissions"
   - Click "Save"

### Tag Already Exists

**Problem**: You need to recreate a release for an existing tag.

**Solution**:
```bash
# Delete the tag locally
git tag -d v5.0.1

# Delete the tag remotely
git push origin :refs/tags/v5.0.1

# Delete the release on GitHub (via web interface)
# Then recreate the tag and push
git tag -a v5.0.1 -m "Release version 5.0.1"
git push origin v5.0.1
```

### Missing Files in Release Package

**Problem**: The release package is missing expected files.

**Solution**: Review the "Create distribution package" step in `.github/workflows/release.yml` and ensure all necessary files are copied.

## Manual Release (Fallback)

If the automated workflow fails, you can create a release manually:

1. Build the distribution package locally:
   ```bash
   composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
   mkdir -p dist/Matomo
   cp extension.json README.md dist/Matomo/
   cp -r src vendor dist/Matomo/
   cd dist && zip -r matomo-mediawiki-extension-v5.0.1.zip Matomo
   sha256sum matomo-mediawiki-extension-v5.0.1.zip > matomo-mediawiki-extension-v5.0.1.zip.sha256
   ```

2. Create a release on GitHub:
   - Go to Releases → "Draft a new release"
   - Select the tag (or create a new one)
   - Add release notes
   - Upload the zip file and checksum
   - Publish the release

## Support

For questions or issues with the release process, please:

1. Check the [GitHub Actions documentation](https://docs.github.com/en/actions)
2. Open an issue in the repository
3. Contact the maintainers

## Changelog

Maintain a `CHANGELOG.md` file (optional) to track changes between versions. This helps users understand what has changed and makes release notes easier to write.
