module.exports = {
  outDir: 'out-autonomous',
  hooks: {
    prePackage: async () => require('./prepare-bundle.cjs').prepareBundle(),
  },
  packagerConfig: {
    asar: true,
    extraResource: ['runtime', 'desktop-bundle'],
    ignore: [/^\/runtime(?:\/|$)/, /^\/desktop-bundle(?:-|\/|$)/, /^\/out(?:\/|$)/, /^\/out-autonomous(?:\/|$)/],
  },
  makers: [
    {
      name: '@electron-forge/maker-squirrel',
      config: {
        authors: 'Atelier Mosaïque',
        description: 'Application autonome de création de mosaïques.',
      },
    },
  ],
};
