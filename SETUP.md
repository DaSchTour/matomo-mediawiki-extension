# Setup Instructions for Automated Releases

This document explains what needs to be configured in the GitHub repository to enable automated releases.

## Repository Settings

The automated release process is now configured and ready to use. However, you should verify the following settings in your GitHub repository:

### 1. GitHub Actions Permissions

Ensure GitHub Actions has permission to create releases:

1. Go to **Settings** → **Actions** → **General**
2. Scroll down to **Workflow permissions**
3. Select **"Read and write permissions"**
4. Check **"Allow GitHub Actions to create and approve pull requests"** (optional, but recommended)
5. Click **Save**

### 2. Branch Protection (Optional but Recommended)

To ensure quality releases, consider setting up branch protection:

1. Go to **Settings** → **Branches**
2. Click **Add rule** for the `master` branch
3. Configure the following:
   - ✅ Require a pull request before merging
   - ✅ Require status checks to pass before merging
     - Select the CI workflow checks
   - ✅ Require branches to be up to date before merging
4. Click **Create** or **Save changes**

## Workflow Files

The following workflow files have been added:

### `.github/workflows/release.yml`

This workflow:
- **Triggers on**: Push of tags matching `v*` pattern (e.g., `v5.0.1`)
- **Actions performed**:
  1. Checks out the code
  2. Sets up PHP 7.4
  3. Installs production dependencies with Composer
  4. Creates a distribution package (zip file) with only necessary files
  5. Generates SHA256 checksum for verification
  6. Creates a GitHub release with auto-generated notes
  7. Uploads the distribution package and checksum as release assets

### `.github/workflows/ci.yml`

This existing workflow:
- Runs on every push and pull request
- Tests the code against multiple PHP versions (7.3, 7.4, 8.0, 8.1, 8.2)
- Ensures code quality before merging

## Documentation Added

### RELEASE.md

Complete guide for creating releases:
- Step-by-step instructions
- Version numbering guidelines
- Troubleshooting tips
- Manual release fallback procedures

### CHANGELOG.md

Template for tracking changes between versions following the [Keep a Changelog](https://keepachangelog.com/) format.

### Updated README.md

- Added badges for latest release, CI status, and license
- Added installation instructions for release packages
- Updated to reflect the new release process

### GitHub Templates

- **Pull Request Template** (`.github/pull_request_template.md`)
  - Reminds contributors to update CHANGELOG
  - Provides a structured format for PRs
  
- **Release Issue Template** (`.github/ISSUE_TEMPLATE/release.md`)
  - Provides a checklist for creating releases
  - Ensures all steps are followed

## How to Create a Release

Once the above settings are verified, creating a release is simple:

1. **Update version numbers** in `extension.json` and `README.md`
2. **Update CHANGELOG.md** with changes for the new version
3. **Commit and push** changes to master
4. **Create and push a tag**:
   ```bash
   git tag -a v5.0.1 -m "Release version 5.0.1"
   git push origin v5.0.1
   ```
5. **Monitor** the GitHub Actions workflow
6. **Verify** the release on the [Releases page](https://github.com/DaSchTour/matomo-mediawiki-extension/releases)

See [RELEASE.md](RELEASE.md) for detailed instructions.

## Testing the Release Workflow

You can test the release workflow without creating a real release:

### Option 1: Create a Test Tag

```bash
# Create a pre-release tag
git tag -a v5.0.1-rc.1 -m "Release candidate 5.0.1-rc.1"
git push origin v5.0.1-rc.1
```

This will trigger the workflow and create a release. You can then:
- Verify the workflow completes successfully
- Check that the release package is created correctly
- Delete the test release and tag afterwards

### Option 2: Manual Workflow Dispatch (Future Enhancement)

Consider adding a `workflow_dispatch` trigger to the release workflow for manual testing:

```yaml
on:
  push:
    tags:
      - 'v*'
  workflow_dispatch:
    inputs:
      version:
        description: 'Version to release (e.g., 5.0.1)'
        required: true
```

## Troubleshooting

### Workflow doesn't trigger

**Possible causes:**
- Tag doesn't match the pattern `v*`
- GitHub Actions is disabled for the repository
- Workflow permissions are insufficient

**Solution:**
1. Check that the tag follows the format `vX.Y.Z`
2. Verify GitHub Actions is enabled in repository settings
3. Check workflow permissions (see step 1 above)

### Release creation fails

**Possible causes:**
- Insufficient permissions
- Network issues downloading dependencies
- Invalid syntax in workflow file

**Solution:**
1. Check the Actions logs for specific error messages
2. Verify repository has write permissions enabled
3. Validate the YAML syntax of the workflow file

### Distribution package is incorrect

**Possible causes:**
- Missing files in the copy step
- Incorrect directory structure

**Solution:**
1. Review the "Create distribution package" step in the workflow
2. Test the commands locally:
   ```bash
   composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
   mkdir -p dist/Matomo
   cp extension.json README.md dist/Matomo/
   cp -r src vendor dist/Matomo/
   cd dist && zip -r test-package.zip Matomo
   ```
3. Verify the package structure matches expectations

## Maintenance

### Updating the Workflow

To modify the release workflow:

1. Edit `.github/workflows/release.yml`
2. Test changes with a test tag or in a fork
3. Update documentation if the process changes

### Keeping Dependencies Updated

Regularly update the GitHub Actions used in workflows:

- `actions/checkout@v4` → Check for updates
- `shivammathur/setup-php@v2` → Check for updates  
- `softprops/action-gh-release@v1` → Check for updates

Use Dependabot or Renovate to automate dependency updates.

## Security Considerations

- The workflow uses `GITHUB_TOKEN` which is automatically provided by GitHub Actions
- No secrets need to be configured manually
- The token has limited permissions scoped to the repository
- Release packages are built from source with verified dependencies
- Checksums (SHA256) are provided for package verification

## Support

For questions or issues:

1. Check [RELEASE.md](RELEASE.md) for detailed release instructions
2. Review [GitHub Actions documentation](https://docs.github.com/en/actions)
3. Check the [Actions tab](https://github.com/DaSchTour/matomo-mediawiki-extension/actions) for workflow logs
4. Open an issue in the repository for help

## Next Steps

After merging this PR:

1. ✅ Verify repository settings (especially workflow permissions)
2. ✅ Update version numbers for the next release
3. ✅ Create a test release to verify everything works
4. ✅ Document any custom release procedures specific to your workflow
5. ✅ Consider setting up branch protection rules
6. ✅ Share release process documentation with maintainers
