# Recommended Revisions

[![Extension/Skin Tests](https://github.com/vm-pranavan/RecommendedRevisions/actions/workflows/ci.yml/badge.svg?branch=ci/add-extension-tests)](https://github.com/vm-pranavan/RecommendedRevisions/actions/workflows/ci.yml)

Recommended Revisions is a project to try to find a consensus-based listing of the ideal revision to use for any extension or skin, for different MediaWiki versions. This repository holds YAML files containing the actual data for this project.

You can see more information at the project's homepage, [mediawiki.org/wiki/Recommended_Revisions](https://www.mediawiki.org/wiki/Recommended_Revisions).

---

## Continuous Integration (CI) Pipeline

To ensure the recommended extensions and skins compile, load, and pass their unit tests correctly under the targeted MediaWiki versions, a robust, parallelized CI pipeline has been established.

### Architecture Overview

1. **YAML Manifest Parsing:** A custom Python parser (`scripts/parse_yaml.py`) processes the target YAML file (e.g., `1.43.yaml`) to:
   * Validate all entry structures and check for missing required dependencies.
   * Sort all extensions and skins topologically (using Kahn's Algorithm) so that parent extensions load before their dependents.
   * Implement transitive skipping so that if a parent extension is skipped, all its child dependents are automatically bypassed to avoid cascade failures.
   * Divide the 170+ extensions into **5 parallel batches** (`bundled`, `smw-ecosystem`, `standalone-a-l`, `standalone-m-z`, and `skins`).
2. **Parallelized Docker Test Suites:** In GitHub Actions, five concurrent runners spin up MediaWiki docker environments with RAM-backed MariaDB databases (`tmpfs`) to:
   * Perform high-speed repository clones and checkout matching commit SHAs.
   * Run dependency updates (`composer update`).
   * Run individual PHPUnit and parser tests isolated per extension to collect JUnit XML reports.
3. **Reporting:** Aggregates all test results and writes a rich markdown summary directly into the GitHub Actions run dashboard.

### Running Tests Locally

You can run the full validation and test suite locally using Docker Compose.

#### 1. Validate the YAML manifest
```bash
python3 scripts/parse_yaml.py 1.43.yaml --validate
```

#### 2. Run a specific test batch using Docker Compose
```bash
# Start the fast, RAM-backed MediaWiki container stack
MW_VERSION=1.43 docker compose -f .ci/docker-compose.ci.yml up -d

# Install the extension batch (e.g., smw-ecosystem)
docker compose -f .ci/docker-compose.ci.yml exec mediawiki bash /ci/install_extensions.sh 1.43.yaml smw-ecosystem

# Execute the PHPUnit tests for the batch
docker compose -f .ci/docker-compose.ci.yml exec mediawiki bash /ci/run_tests.sh smw-ecosystem

# Spin down the stack
docker compose -f .ci/docker-compose.ci.yml down
```

#### 3. Skip List Configuration
If an extension requires an active external service (e.g., Elasticsearch, LDAP identity providers, AWS credentials) or has a known buggy/incompatible integration test suite, it can be bypassed by adding it to [`.ci/skip_list.yaml`](.ci/skip_list.yaml) along with a documented reason.
