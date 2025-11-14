name: Release Checklist
about: Checklist for creating a new release
title: 'Release v[VERSION]'
labels: release
assignees: ''

---

## Pre-Release Checklist

- [ ] All planned features/fixes for this release are merged
- [ ] All CI checks pass on master branch
- [ ] Update version number in `extension.json`
- [ ] Update version and date in `README.md`
- [ ] Update `CHANGELOG.md` with release notes
- [ ] Review and update documentation if needed

## Version Information

**Target Version:** vX.Y.Z
**Type:** Major / Minor / Patch
**Release Date:** YYYY-MM-DD

## Changes in This Release

### Added
- 

### Changed
- 

### Fixed
- 

### Security
- 

## Release Steps

- [ ] Create and push version tag: `git tag -a vX.Y.Z -m "Release version X.Y.Z" && git push origin vX.Y.Z`
- [ ] Monitor the [Release workflow](../actions/workflows/release.yml)
- [ ] Verify the release on the [Releases page](../releases)
- [ ] Edit release notes if needed
- [ ] Announce the release (if applicable)

## Post-Release

- [ ] Verify installation instructions work with the new release
- [ ] Update any external documentation or references
- [ ] Close related issues and PRs
