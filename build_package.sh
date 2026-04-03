#!/bin/bash
#
# Build script for Atomic Joomla Package
# Produces: pkg_atomic_VERSION.zip (package with template + sample data plugin)
#           tpl_atomic_VERSION.zip (standalone template)
#           plg_sampledata_atomic_VERSION.zip (standalone plugin)
#
# Usage: bash build_package.sh
#

set -e

# ── Paths ──────────────────────────────────────────────────────────
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
TPL_SRC="$SCRIPT_DIR/tpl_atomic"
PLG_SRC="$SCRIPT_DIR/plg_sampledata_atomic"
PKG_SRC="$SCRIPT_DIR/pkg_atomic"
OUT_DIR="$SCRIPT_DIR/ZIP"

# ── Versions (read from XML manifests) ─────────────────────────────
TPL_VERSION=$(sed -n 's/.*<version>\([^<]*\)<\/version>.*/\1/p' "$TPL_SRC/templateDetails.xml" | head -1)
PLG_VERSION=$(sed -n 's/.*<version>\([^<]*\)<\/version>.*/\1/p' "$PLG_SRC/atomic.xml" | head -1)
PKG_VERSION=$(sed -n 's/.*<version>\([^<]*\)<\/version>.*/\1/p' "$PKG_SRC/pkg_atomic.xml" | head -1)

# ── Common zip exclusions ──────────────────────────────────────────
EXCLUDES=(-x "*.DS_Store" -x "__MACOSX/*" -x ".git/*")

# ── Clean .DS_Store files from source directories ──────────────────
echo "Cleaning .DS_Store files..."
find "$TPL_SRC" "$PLG_SRC" "$PKG_SRC" -name ".DS_Store" -delete 2>/dev/null || true

# ── Create output directory ────────────────────────────────────────
mkdir -p "$OUT_DIR"

# ── Temp workspace ─────────────────────────────────────────────────
WORK_DIR=$(mktemp -d)
trap 'rm -rf "$WORK_DIR"' EXIT

echo "========================================"
echo "  Atomic Package Builder"
echo "========================================"
echo "  Template version : $TPL_VERSION"
echo "  Plugin version   : $PLG_VERSION"
echo "  Package version  : $PKG_VERSION"
echo "----------------------------------------"

# ── 1. Build standalone template ZIP ───────────────────────────────
echo ""
echo "[1/3] Building standalone template ZIP..."
TPL_ZIP="$OUT_DIR/tpl_atomic_${TPL_VERSION}.zip"
rm -f "$TPL_ZIP"
(cd "$TPL_SRC" && zip -r "$TPL_ZIP" . "${EXCLUDES[@]}" -q)
echo "  -> $TPL_ZIP"

# ── 2. Build plugin ZIP ───────────────────────────────────────────
echo ""
echo "[2/3] Building sample data plugin ZIP..."
PLG_ZIP="$OUT_DIR/plg_sampledata_atomic_${PLG_VERSION}.zip"
rm -f "$PLG_ZIP"
(cd "$PLG_SRC" && zip -r "$PLG_ZIP" . "${EXCLUDES[@]}" -q)
echo "  -> $PLG_ZIP"

# ── 3. Assemble package ZIP ───────────────────────────────────────
echo ""
echo "[3/3] Assembling package ZIP..."

# Create package structure in temp dir
mkdir -p "$WORK_DIR/packages"
cp "$PKG_SRC/pkg_atomic.xml" "$WORK_DIR/"
cp "$TPL_ZIP" "$WORK_DIR/packages/tpl_atomic.zip"
cp "$PLG_ZIP" "$WORK_DIR/packages/plg_sampledata_atomic.zip"

PKG_ZIP="$OUT_DIR/pkg_atomic_${PKG_VERSION}.zip"
rm -f "$PKG_ZIP"
(cd "$WORK_DIR" && zip -r "$PKG_ZIP" . "${EXCLUDES[@]}" -q)
echo "  -> $PKG_ZIP"

# ── 4. Update SHA-256 checksum in update XML files ────────────────
echo ""
echo "[4/4] Updating SHA-256 checksums in update XML files..."
PKG_SHA256=$(shasum -a 256 "$PKG_ZIP" | awk '{print $1}')
DOCS_DIR="$SCRIPT_DIR/docs"

for XML_FILE in "$DOCS_DIR/update.xml" "$DOCS_DIR/update-beta.xml"; do
	if [ -f "$XML_FILE" ]; then
		sed -i '' "s|<sha256>[^<]*</sha256>|<sha256>${PKG_SHA256}</sha256>|" "$XML_FILE"
		echo "  -> Updated $(basename "$XML_FILE")"
	fi
done

# ── Summary ────────────────────────────────────────────────────────
echo ""
echo "========================================"
echo "  Build complete!"
echo "========================================"
echo ""
echo "  Package (install this):"
echo "    $PKG_ZIP"
echo ""
echo "  SHA-256: $PKG_SHA256"
echo ""
echo "  Standalone ZIPs (also in $OUT_DIR):"
echo "    tpl_atomic_${TPL_VERSION}.zip"
echo "    plg_sampledata_atomic_${PLG_VERSION}.zip"
echo ""
