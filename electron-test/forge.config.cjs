module.exports = {
  packagerConfig: {
    asar: true,
  },
  makers: [
    {
      name: '@electron-forge/maker-squirrel',
      config: {
        authors: 'Atelier Mosaïque',
        description: 'Lanceur de test de l’application locale Mon atelier de mosaïques.',
      },
    },
  ],
};
