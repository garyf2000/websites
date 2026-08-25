# en-react (ExtremeCloud One design system) — sync conventions

Source: `libs/en-v3.0/en-react` v0.1.58, synced from its Storybook build. 156 components,
mounted at `window.EnReact`. Every export is `EN`-prefixed (`ENButton`, `ENAlert`, …) and
wraps a Lit web component shipped by the sibling `en-web-components` package.

## Theming — always provide a `.theme-light` (or `.theme-dark`) ancestor class

All design tokens (`--en-theme-*`, `--en-color-base-*` — 575 CSS custom properties total)
are defined by en-web-components under a **class selector**, not `:root`. If you preview
or embed a component without a `.theme-light`/`.theme-dark` class somewhere in its
ancestor chain, every `--en-theme-*`-scoped style resolves to nothing — components render
with missing backgrounds/borders/colors, often silently (no console error). This sync's
own bundle works around it by promoting the token rule to also match `:root`, but any
other consumer of these components needs the real theme class applied explicitly.

## Preview canvas is white; components are designed for a dark canvas

Every card in this sync renders on a plain white background. Many components use
text/border colors chosen for legibility against a *dark* canvas and look
washed-out or faint here — that's a display artifact of the preview harness, not
a component defect. Judge these components against a dark background before
concluding something is "wrong."

## `ENDataGrid` needs `_vendor/ag-grid.js` loaded before `_ds_bundle.js`

`ag-grid-community`/`ag-grid-enterprise` (~5.2MB) are vendored into their own file,
`_vendor/ag-grid.js`, the same way React is — real code, no stub, no CDN. Load it with a
plain `<script>` tag *before* `_ds_bundle.js`:

```html
<script src="_vendor/react.js"></script>
<script src="_vendor/react-dom.js"></script>
<script src="_vendor/ag-grid.js"></script>
<script src="_ds_bundle.js"></script>
```

If `_vendor/ag-grid.js` isn't loaded first, `window.EnReact.ENDataGrid`'s grid body will
throw when it tries to create the grid instance. All 156 shipped preview cards already
include this script tag in the right order — only a from-scratch integration needs to
add it manually.

## Known open visual mismatches (not yet root-caused)

A handful of components have confirmed, real rendering differences from their source
Storybook that weren't chased down further (see the repo's `.design-sync/NOTES.md` for
full detail if you're continuing this investigation):

- **ENPopover** — floating panel can render off-screen (computed position places it
  outside the viewport).
- **ENControlButtonGroup** ("Gap Small" story) — a floating `ENPanel` child renders
  outside its expected bounds.
- **ENColorPicker** — the "no color" swatch shows a solid square instead of the
  checkered transparency pattern.
- **ENRepeatScheduler** — the day-of-month/day-of-week grid is sometimes unstyled/empty.
- **ENRfMiniMap** family — the floor-plan background image doesn't render (the `map`
  prop is a slotted HTML template whose default content isn't resolving); marker
  positions are consequently off.
- **ENSegmentButton** — unselected/disabled states are missing their background and
  border chrome entirely (distinct from the white-canvas fade above — this one is a
  real absence of styling, confirmed by pixel sampling).
- **ENWidgetTile** ("Selected" story) — missing the card border/rounded-corner chrome
  that the source shows for the selected state.
- **ENRfMiniMapThershold** — renders almost entirely blank (only a small icon visible).

## Components with no gradable render at all

`ENQueryBuilder`, `ENQueryFields`, `ENQueryGroup`, `ENToast`, `ENVirtualizedList`,
`ENVirtualizedTreeCheckbox`, `ENBackdrop`, `ENLogin` fail to render in the *source*
Storybook too (confirmed by isolated reproduction against the reference build) — this is
a pre-existing gap in the component library's own Storybook setup, not something
introduced by this sync. `ENCropper` uses a Storybook `loaders` feature (async
pre-render data fetching) this sync's converter doesn't support and is skipped for the
same reason.

# EnReact (libs/en-v3.0/en-react@0.1.58)

This design system is the published libs/en-v3.0/en-react React library, bundled as a single
browser global. All 751 components are the real upstream code.

## Where things are

- `_ds_bundle.js` — the whole-DS bundle at the project root; loads every component to `window.EnReact`. First line is a `/* @ds-bundle: … */` metadata header.
- `styles.css` — the single stylesheet entry: it `@import`s the tokens, fonts, and component styles (`_ds_bundle.css`). Link this one file.
- `components/<group>/<Name>/<Name>.prompt.md` (example JSX + variants), `<Name>.d.ts` (types), `<Name>.html` (variant grid).
- `tokens/*.css` — CSS custom properties, names verbatim from upstream.
- `fonts/` — `@font-face` files + `fonts.css` (when the package ships fonts).

For a specific component, `read_file("components/<group>/<Name>/<Name>.prompt.md")`.

## Loading

Add these two lines to your page once (React must be on the page first):

```html
<link rel="stylesheet" href="styles.css">
<script src="_ds_bundle.js"></script>
```

Components are then available at `window.EnReact.*`. Mount into a dedicated child node (e.g. `<div id="ds-root">`), not the host page's own React root, so the two trees don't collide:

```jsx
const { ENAccordion } = window.EnReact;
ReactDOM.createRoot(document.getElementById('ds-root')).render(<ENAccordion />);
```

## Tokens

580 CSS custom properties from libs/en-v3.0/en-react. Names are
preserved verbatim from upstream. They are declared inside `_ds_bundle.css` (this DS ships one compiled stylesheet rather than separate token files).

- **color** (416): `--en-color-base-red-1`, `--en-color-base-red-2`, `--en-color-base-red-3`, …
- **spacing** (3): `--en-ag-cell-horizontal-padding`, `--en-ag-widget-container-horizontal-padding`, `--en-ag-widget-container-vertical-padding`
- **typography** (31): `--en-font-family-primary-figma`, `--en-font-family-primary`, `--en-line-height-0`, …
- **radius** (15): `--en-border-radius-xs`, `--en-border-radius-sm`, `--en-border-radius-md`, …
- **shadow** (15): `--en-box-shadow-xs`, `--en-box-shadow-sm`, `--en-box-shadow-md`, …
- **other** (100): `--en-anim-fade-quick`, `--en-anim-fade-long`, `--en-anim-move-quick`, …

## Components

### components
- `ENAccordion`
- `ENAccordionPanel`
- `ENAlert`
- `ENAutoSuggest`
- `ENAvatar`
- `ENAvatarGroup`
- `ENBackdrop`
- `ENBadge`
- `ENBadgeGroup`
- `ENBanner`
- `ENBreadcrumbs`
- `ENBreadcrumbsItem`
- `ENButton`
- `ENButtonGroup`
- `ENCard`
- `ENCarousel`
- `ENChart`
- `ENChartPanel`
- `ENCheckbox`
- `ENCheckboxItem`
- `ENChip`
- `ENChipGroup`
- `ENCliEditor`
- `ENColorPicker`
- `ENConfigurationScreen`
- `ENContextualMenu`
- `ENControlButton`
- `ENControlButtonGroup`
- `ENCropper`
- `ENDataBlock`
- `ENDataBlockPanel`
- `ENDataGrid`
- `ENDatepickerField`
- `ENDatetimepickerField`
- `ENDialog`
- `ENDivider`
- `ENDoubleRangeCalendar`
- `ENDrawer`
- `ENDropdown`
- `ENDropdownButton`
- `ENDropdownPanel`
- `ENDropdownSelectionPanel`
- `ENEmptyState`
- `ENErrorPage`
- `ENExtremeAiExpertButton`
- `ENFieldNote`
- `ENFileUpload`
- `ENFlag`
- `ENFlatGauge`
- `ENFloatingTooltip`
- `ENGauge`
- `ENGaugeContainer`
- `ENGrid`
- `ENHeading`
- `ENImagePlaceholder`
- `ENInlineTimeSelector`
- `ENInput`
- `ENInputStepper`
- `ENKeyValueItem`
- `ENKeyValueList`
- `ENLayout`
- `ENLayoutContainer`
- `ENLink`
- `ENList`
- `ENListGroup`
- `ENListItem`
- `ENLoadingIndicator`
- `ENLogin`
- `ENMenu`
- `ENMenuItem`
- `ENMicroWidgetAreaGraph`
- `ENMicroWidgetBarGraph`
- `ENMicroWidgetSegment`
- `ENMicroWidgetSegmentedBar`
- `ENMicroWidgetShell`
- `ENMicroWidgetTile`
- `ENMiniMap`
- `ENMiniMapLayer`
- `ENMiniMapMap`
- `ENMultiSelectDropdown`
- `ENNavigationMenu`
- `ENNotification`
- `ENNotificationCenter`
- `ENNotificationGroup`
- `ENNotificationsIndicator`
- `ENOgmaContainer`
- `ENOnboardingScreen`
- `ENPagination`
- `ENPaginationItem`
- `ENPanel`
- `ENPhoneInput`
- `ENPopover`
- `ENPort`
- `ENPortGroup`
- `ENPortsPanel`
- `ENProductLogo`
- `ENProgress`
- `ENProgressTextLoader`
- `ENQueryBuilder`
- `ENQueryFields`
- `ENQueryGroup`
- `ENRadio`
- `ENRadioItem`
- `ENRangeCalendar`
- `ENRepeatScheduler`
- `ENRfMiniMap`
- `ENRfMiniMapFilter`
- `ENRfMiniMapGradientChannel`
- `ENRfMiniMapIndicator`
- `ENRfMiniMapPanel`
- `ENRfMiniMapThershold`
- `ENRfMiniMapThreads`
- `ENRfMiniMapVisibilityList`
- `ENSearchField`
- `ENSearchForm`
- `ENSegmentButton`
- `ENSegmentButtonGroup`
- `ENSelect`
- `ENSelectableTile`
- `ENSelectableTileGroup`
- `ENSelectPanel`
- `ENSideMenu`
- `ENSingleStackedChart`
- `ENSkeleton`
- `ENSlider`
- `ENStepper`
- `ENStepperItem`
- `ENSubTab`
- `ENSubTabs`
- `ENSwitch`
- `ENTab`
- `ENTabPanel`
- `ENTabs`
- `ENTextarea`
- `ENTextField`
- `ENTextPassage`
- `ENTimeline`
- `ENTimelineItem`
- `ENTimePicker`
- `ENTimezoneDropdown`
- `ENToast`
- `ENToastGroup`
- `ENTooltip`
- `ENTransferList`
- `ENTreeCheckbox`
- `ENTreeDropdown`
- `ENTreeDropdownPanel`
- `ENVirtualizedList`
- `ENVirtualizedTreeCheckbox`
- `ENVirtualizedTreeDropdown`
- `ENVirtualizedTreeDropdownPanel`
- `ENWidgetShell`
- `ENWidgetShellItem`
- `ENWidgetTile`

### boilerplate
- `ENCalendar`
- `ENTimeSelectorList`

### icons
- `ENIconAccessPoint`
- `ENIconAccessPointExtreme`
- `ENIconAccessPointGeneric`
- `ENIconAdd`
- `ENIconAddSquare`
- `ENIconAddUser`
- `ENIconAdvisorAgentBeta`
- `ENIconAfcStatus`
- `ENIconAiAgentsHubBeta`
- `ENIconAiAgentTag`
- `ENIconAiCanvas`
- `ENIconAiCredits`
- `ENIconAiDialog`
- `ENIconAiFullscreen`
- `ENIconAiGenerated`
- `ENIconAipsLogo`
- `ENIconAiSidebar`
- `ENIconAiWorkbench`
- `ENIconAlertDisable`
- `ENIconAlertRing`
- `ENIconAlertSettings`
- `ENIconAlertWarning`
- `ENIconAllRoutesImport`
- `ENIconAlphabetical`
- `ENIconAnchorAp`
- `ENIconAntenna1`
- `ENIconApi`
- `ENIconApplications`
- `ENIconArray`
- `ENIconArrowDown`
- `ENIconArrowLeft`
- `ENIconArrowRight`
- `ENIconArrowTurnRight`
- `ENIconArrowUp`
- `ENIconAttachment`
- `ENIconAuditLogs`
- `ENIconAutoSense`
- `ENIconBadge`
- `ENIconBattery100Percent`
- `ENIconBattery20Percent`
- `ENIconBattery70Percent`
- `ENIconBatteryCharging100Percent`
- `ENIconBatteryCharging20Percent`
- `ENIconBatteryCharging70Percent`
- `ENIconBell`
- `ENIconBlock`
- `ENIconBluetooth`
- `ENIconBook`
- `ENIconBookmark`
- `ENIconBooks`
- `ENIconBoolean`
- `ENIconBridge`
- `ENIconBrowserAlert`
- `ENIconBrowserChrome`
- `ENIconBuilding`
- `ENIconBuilding2`
- `ENIconBulb`
- `ENIconCalendar`
- `ENIconCalendar2`
- `ENIconCameraSurveillance`
- `ENIconCartEmpty`
- `ENIconCartFull`
- `ENIconCautionFilled`
- `ENIconCells`
- `ENIconCelsius`
- `ENIconCenter`
- `ENIconChannelized`
- `ENIconChart`
- `ENIconChartBar`
- `ENIconChartBigNumber`
- `ENIconChartBubble`
- `ENIconChartClock`
- `ENIconChartColumn`
- `ENIconChartDataTable`
- `ENIconChartDonut`
- `ENIconChartDualLine`
- `ENIconChartGeoCountryMap`
- `ENIconChartHistogram`
- `ENIconChartLine`
- `ENIconChartNotification`
- `ENIconChartPie`
- `ENIconCharts`
- `ENIconChartStepline`
- `ENIconChartTimeSeriesBar`
- `ENIconChartTimeSeriesLine`
- `ENIconChartTimeSeriesScatterPlot`
- `ENIconChartWordCloud`
- `ENIconChartWorld`
- `ENIconChat`
- `ENIconChatHistory`
- `ENIconChatNew`
- `ENIconCheckbox`
- `ENIconCheckboxChecked`
- `ENIconChevronDown`
- `ENIconChevronLeft`
- `ENIconChevronRight`
- `ENIconChevronUp`
- `ENIconCircle`
- `ENIconCircles`
- `ENIconClient`
- `ENIconClientPath1`
- `ENIconClientPath2`
- `ENIconClientPath3`
- `ENIconClock`
- `ENIconClose`
- `ENIconCloud`
- `ENIconCoachAgentBeta`
- `ENIconCode`
- `ENIconCodeCopy`
- `ENIconColumn90`
- `ENIconColumns`
- `ENIconColumns1`
- `ENIconColumns2`
- `ENIconColumns3`
- `ENIconColumns4`
- `ENIconComment`
- `ENIconCompass`
- `ENIconComputer`
- `ENIconComputerMac`
- `ENIconConfigurationAuditMatch`
- `ENIconConfigurationAuditMismatch`
- `ENIconConfigurationPending`
- `ENIconConfigurationRollback`
- `ENIconConfiguredAtDeviceLevel`
- `ENIconConflict`
- `ENIconConflictResolved`
- `ENIconConnectedDevice`
- `ENIconContentGenerator`
- `ENIconController`
- `ENIconCookies`
- `ENIconCopperConfigured`
- `ENIconCopperConfiguredSelected`
- `ENIconCopperConflict`
- `ENIconCopperConflictSelected`
- `ENIconCopperConnected`
- `ENIconCopperConnectedSelected`
- `ENIconCopperDeselected`
- `ENIconCopperDisabled`
- `ENIconCopperDisabledSelected`
- `ENIconCopperDisconnected`
- `ENIconCopperDisconnectedSelected`
- `ENIconCopperLagConfigured`
- `ENIconCopperLagConfiguredSelected`
- `ENIconCopperOverridden`
- `ENIconCopperOverriddenSelected`
- `ENIconCopy`
- `ENIconCrown`
- `ENIconCustomerSupport`
- `ENIconCustomize`
- `ENIconDarkMode`
- `ENIconDashboard`
- `ENIconDataCenter`
- `ENIconDelete`
- `ENIconDeploy`
- `ENIconDeviceGroup`
- `ENIconDevices`
- `ENIconDeviceSelector`
- `ENIconDeviceUngroup`
- `ENIconDeviceUpdateUnsuccessful`
- `ENIconDfs`
- `ENIconDigitalTwin`
- `ENIconDisable`
- `ENIconDisconnectedDevice`
- `ENIconDocument`
- `ENIconDocument2`
- `ENIconDone`
- `ENIconDoneCircle`
- `ENIconDoneCircleFilled`
- `ENIconDotsHorizontal`
- `ENIconDotsVertical`
- `ENIconDoubleArrowLeft`
- `ENIconDoubleArrowRight`
- `ENIconDownload`
- `ENIconDragHandle`
- `ENIconDraw`
- `ENIconDrone`
- `ENIconEc`
- `ENIconEcApplianceClusterClosed`
- `ENIconEcApplianceClusterOpen`
- `ENIconEcIqLogo`
- `ENIconEcLogo`
- `ENIconEdit`
- `ENIconEii`
- `ENIconElevator`
- `ENIconElLogo`
- `ENIconEmailClosed`
- `ENIconEmailOpen`
- `ENIconEMark`
- `ENIconEmoji`
- `ENIconEncrypted`
- `ENIconEndpoint`
- `ENIconEnPurple`
- `ENIconEnterpriseRouter`
- `ENIconEnterpriseSwitch`
- `ENIconEp1Logo`
- `ENIconEp1MspLogo`
- `ENIconEp1Networking`
- `ENIconEp1Security`
- `ENIconExcorpBkLogo`
- `ENIconExcorpWhLogo`
- `ENIconExport`
- `ENIconExtremeExchange`
- `ENIconFabric`
- `ENIconFabricAttachIssue`
- `ENIconFabricDisabled`
- `ENIconFabricEnabled`
- `ENIconFaceId`
- `ENIconFaceIdIos`
- `ENIconFanStatus`
- `ENIconFeatureNavigator`
- `ENIconFeedback`
- `ENIconFile`
- `ENIconFileAlert`
- `ENIconFileCsv`
- `ENIconFileDocx`
- `ENIconFileHtml`
- `ENIconFileJpg`
- `ENIconFileJson`
- `ENIconFilePdf`
- `ENIconFilePng`
- `ENIconFilePrqt`
- `ENIconFileSql`
- `ENIconFileTransfer`
- `ENIconFileUnsupported`
- `ENIconFileWebp`
- `ENIconFileXlsx`
- `ENIconFileXml`
- `ENIconFilter`
- `ENIconFilter90`
- `ENIconFilterFilled`
- `ENIconFingerprint`
- `ENIconFips`
- `ENIconFirewall`
- `ENIconFlask`
- `ENIconFloorplan`
- `ENIconFolder`
- `ENIconFolder2`
- `ENIconFrequency`
- `ENIconFridge`
- `ENIconFunction`
- `ENIconGlobalOperations`
- `ENIconGlobe`
- `ENIconGrid3`
- `ENIconGroupedNode`
- `ENIconHand`
- `ENIconHandLocked`
- `ENIconHeatmap`
- `ENIconHelp`
- `ENIconHidden`
- `ENIconHighContrastMode`
- `ENIconHistory`
- `ENIconHostname`
- `ENIconHouse`
- `ENIconImage`
- `ENIconImport`
- `ENIconIncomplete`
- `ENIconIncorrect`
- `ENIconIncorrectFilled`
- `ENIconIndividualRouteImport`
- `ENIconInfo`
- `ENIconInfoFilled`
- `ENIconInsights`
- `ENIconInspect`
- `ENIconInstances`
- `ENIconInternet`
- `ENIconInventory`
- `ENIconIot`
- `ENIconIpAcceptPolicy`
- `ENIconIpPhone`
- `ENIconIpSubnet`
- `ENIconKey`
- `ENIconKeyCursor`
- `ENIconKeyPrimary`
- `ENIconKeyPrimaryCursor`
- `ENIconKnowledgeBase`
- `ENIconLag`
- `ENIconLaptop`
- `ENIconLaptop2`
- `ENIconLaunch`
- `ENIconLayers`
- `ENIconLayout`
- `ENIconLayoutForceLink`
- `ENIconLayoutMasonry`
- `ENIconLayoutShared`
- `ENIconLegend`
- `ENIconLetterA`
- `ENIconLicensedUser`
- `ENIconLightMode`
- `ENIconLink`
- `ENIconLinkBlocked`
- `ENIconLinkDownThick`
- `ENIconLinkDownThin`
- `ENIconList`
- `ENIconLldpSwitch`
- `ENIconLocalActiveDirectory`
- `ENIconLocallyManagedNoXiq`
- `ENIconLocallyManagedXiq`
- `ENIconLocationPin`
- `ENIconLocationPinFilled`
- `ENIconLocationSecurity`
- `ENIconLock`
- `ENIconLockAlert`
- `ENIconMajorFilled`
- `ENIconMap`
- `ENIconMaximize`
- `ENIconMenu`
- `ENIconMenuTree`
- `ENIconMessage`
- `ENIconMessage2`
- `ENIconMicrophone`
- `ENIconMicrowave`
- `ENIconMinimize`
- `ENIconMinorFilled`
- `ENIconMinus`
- `ENIconMinusSquare`
- `ENIconMirror`
- `ENIconMobile`
- `ENIconMobileClock`
- `ENIconMobileSearch`
- `ENIconMode`
- `ENIconMom`
- `ENIconMonitoringUnassociatedClients`
- `ENIconMove`
- `ENIconMoveUpLeft`
- `ENIconMpls`
- `ENIconMplsIpVpn`
- `ENIconMspLogo`
- `ENIconMultiFactorAuthenticator`
- `ENIconNac`
- `ENIconNearMe`
- `ENIconNetwork`
- `ENIconNetworkAccountSwitcher`
- `ENIconNewChat`
- `ENIconNewSession`
- `ENIconNineDot`
- `ENIconNoApplication`
- `ENIconNoConnection`
- `ENIconNodes`
- `ENIconObject`
- `ENIconOldOsPersonality`
- `ENIconOldOsPersonalityInactive`
- `ENIconOnboard`
- `ENIconOrg1`
- `ENIconOrg2`
- `ENIconOverride`
- `ENIconPhoneData`
- `ENIconPicker`
- `ENIconPin`
- `ENIconPinFilled`
- `ENIconPinOutline`
- `ENIconPipeline`
- `ENIconPlug`
- `ENIconPort`
- `ENIconPower`
- `ENIconPowerSupply`
- `ENIconPresentation`
- `ENIconPrint`
- `ENIconProtect`
- `ENIconProtocol`
- `ENIconProvisionedDevice`
- `ENIconQrBarcode`
- `ENIconQsfpConfigured`
- `ENIconQsfpConfiguredSelected`
- `ENIconQsfpConflict`
- `ENIconQsfpConflictSelected`
- `ENIconQsfpConnected`
- `ENIconQsfpConnectedSelected`
- `ENIconQsfpDeselected`
- `ENIconQsfpDisabled`
- `ENIconQsfpDisabledSelected`
- `ENIconQsfpDisconnected`
- `ENIconQsfpDisconnectedSelected`
- `ENIconQsfpOverridden`
- `ENIconQsfpOverriddenSelected`
- `ENIconRadio`
- `ENIconRadioChecked`
- `ENIconRadioCopy`
- `ENIconRadsecProxyServer`
- `ENIconRecalibrate`
- `ENIconRecenterZoom`
- `ENIconRedo`
- `ENIconRefresh`
- `ENIconRepeater`
- `ENIconReserved`
- `ENIconReset`
- `ENIconRfBookshelf`
- `ENIconRfBrick`
- `ENIconRfConcrete`
- `ENIconRfCubicle`
- `ENIconRfDoorThick`
- `ENIconRfDoorThin`
- `ENIconRfDrywall`
- `ENIconRfElevatorShaft`
- `ENIconRfWindowThick`
- `ENIconRfWindowThin`
- `ENIconRoaming`
- `ENIconRogueAp`
- `ENIconRogueApMigrationOn`
- `ENIconRotateRight`
- `ENIconRouter`
- `ENIconRouterExtreme`
- `ENIconRouterGeneric`
- `ENIconRouterGroup`
- `ENIconRouting`
- `ENIconRows`
- `ENIconSatellite`
- `ENIconSave`
- `ENIconSdWanAppliance`
- `ENIconSdwLogo`
- `ENIconSearch`
- `ENIconSearchClock`
- `ENIconSearchSite`
- `ENIconSecureDevices`
- `ENIconSecurity`
- `ENIconSend`
- `ENIconSensorEngine`
- `ENIconSensorModeInterfaceActive`
- `ENIconSensorModeInterfaceInactive`
- `ENIconServer`
- `ENIconServiceAgentBeta`
- `ENIconServiceConnector`
- `ENIconServiceProfile`
- `ENIconServices`
- `ENIconSettings`
- `ENIconSettingsFilled`
- `ENIconSettingsFilledDotWarning`
- `ENIconSettingsSquare`
- `ENIconSeverityCritical`
- `ENIconSeverityCriticalFilled`
- `ENIconSeverityMedium`
- `ENIconSeverityMediumFilled`
- `ENIconSeverityWarning`
- `ENIconSeverityWarningFilled`
- `ENIconSfpConfigured`
- `ENIconSfpConfiguredSelected`
- `ENIconSfpConflict`
- `ENIconSfpConflictSelected`
- `ENIconSfpConnected`
- `ENIconSfpConnectedSelected`
- `ENIconSfpDdConfigured`
- `ENIconSfpDdConfiguredSelected`
- `ENIconSfpDdConflict`
- `ENIconSfpDdConflictSelected`
- `ENIconSfpDdConnected`
- `ENIconSfpDdConnectedSelected`
- `ENIconSfpDdDeselected`
- `ENIconSfpDdDisabled`
- `ENIconSfpDdDisabledSelected`
- `ENIconSfpDdDisconnected`
- `ENIconSfpDdDisconnectedSelected`
- `ENIconSfpDdOverridden`
- `ENIconSfpDdOverriddenSelected`
- `ENIconSfpDeselected`
- `ENIconSfpDisabled`
- `ENIconSfpDisabledSelected`
- `ENIconSfpDisconnected`
- `ENIconSfpDisconnectedSelected`
- `ENIconSfpOverridden`
- `ENIconSfpOverriddenSelected`
- `ENIconShare`
- `ENIconSharedFeatures`
- `ENIconShieldCheck`
- `ENIconSignal`
- `ENIconSignalHealthy`
- `ENIconSignIn`
- `ENIconSignOut`
- `ENIconSimulatedDevice`
- `ENIconSingleArrowLeft`
- `ENIconSingleArrowRight`
- `ENIconSocialFacebookCircle`
- `ENIconSocialInstagramCircle`
- `ENIconSocialLinkedinCircle`
- `ENIconSocialXCircle`
- `ENIconSocialYoutubeCircle`
- `ENIconSparkle`
- `ENIconSparkline2`
- `ENIconSpectrumIntelligence`
- `ENIconStar`
- `ENIconStarFilled`
- `ENIconStarHalf`
- `ENIconStockCart`
- `ENIconStop`
- `ENIconStopFilled`
- `ENIconStorage`
- `ENIconSuperVlan`
- `ENIconSwapForRealDevice`
- `ENIconSWarning`
- `ENIconSwitch`
- `ENIconSwitch2u`
- `ENIconSwitch4k`
- `ENIconSwitch5k`
- `ENIconSwitch7k`
- `ENIconSwitch8k`
- `ENIconSwitchAccount`
- `ENIconSwitchesStack`
- `ENIconSwitchExtreme`
- `ENIconSwitchFabricEnabled`
- `ENIconSwitchGeneric`
- `ENIconSwitching`
- `ENIconSwitching2`
- `ENIconSwitchIsw`
- `ENIconSwitchL2`
- `ENIconSwitchL3`
- `ENIconSwitchStackWarning`
- `ENIconSwitchThirdParty`
- `ENIconSwitchUser`
- `ENIconTable`
- `ENIconTableCross`
- `ENIconTableFilter`
- `ENIconTableSearch`
- `ENIconTablet`
- `ENIconTag`
- `ENIconTarget`
- `ENIconTaskAdd`
- `ENIconTaskGenerate`
- `ENIconTenants`
- `ENIconTerminal`
- `ENIconTexture`
- `ENIconThinkItThroughBeta`
- `ENIconThirdPartyDevices`
- `ENIconThreadCommissionerRunning`
- `ENIconThumbsDown`
- `ENIconThumbsDownFilled`
- `ENIconThumbsUp`
- `ENIconThumbsUpFilled`
- `ENIconTime`
- `ENIconTimestamp`
- `ENIconTopology`
- `ENIconTpme`
- `ENIconTree`
- `ENIconTrendDown`
- `ENIconTrendNoChange`
- `ENIconTrendUp`
- `ENIconTriangleLeft`
- `ENIconTroubleshoot`
- `ENIconTrunk`
- `ENIconTv`
- `ENIconUnassignedTag`
- `ENIconUndetermined`
- `ENIconUndo`
- `ENIconUnlink`
- `ENIconUnlock`
- `ENIconUp`
- `ENIconUpload`
- `ENIconUrl1`
- `ENIconUser`
- `ENIconUserGroup`
- `ENIconUserSubstitute`
- `ENIconUtilityEdit`
- `ENIconUtilityPreview`
- `ENIconUtilityReadOnly`
- `ENIconUtilityReorder`
- `ENIconUztnaLogo`
- `ENIconVariable`
- `ENIconVideo`
- `ENIconView`
- `ENIconVirtualLan`
- `ENIconVirtualPrivateLan`
- `ENIconVisible`
- `ENIconVlan`
- `ENIconVolumeMuted`
- `ENIconVolumeOn`
- `ENIconVpls`
- `ENIconVpnClientTunnelsDown`
- `ENIconVpnClientTunnelsUp`
- `ENIconVpnClientTunnelsUpAndDown`
- `ENIconVpnServerTurnedDown`
- `ENIconVpnServerTurnedUp`
- `ENIconVpnServerTurnedUpAndDown`
- `ENIconVrf`
- `ENIconWalk`
- `ENIconWall1`
- `ENIconWall2`
- `ENIconWan`
- `ENIconWarningCircle`
- `ENIconWarningFilled`
- `ENIconWarningTriangle`
- `ENIconWebhook`
- `ENIconWifi`
- `ENIconWifiOff`
- `ENIconWired`
- `ENIconWireless`
- `ENIconWorkspace`
- `ENIconXcEdWan`
- `ENIconXcUztna`
- `ENIconXiq`
- `ENIconXiqCompanion`
- `ENIconXiqConnect`
- `ENIconXiqCoPilot`
- `ENIconXiqNavigator`
- `ENIconXiqNew`
- `ENIconXiqPilot`
- `ENIconXiqSiteEngine`
- `ENIconZoomIn`
- `ENIconZoomOut`
