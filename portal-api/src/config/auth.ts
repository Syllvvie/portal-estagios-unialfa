export default {
  jwt: {
    secret: process.env.JWT_SECRET ?? "portal_estagios_secret",
    expiresIn: process.env.JWT_EXPIRES_IN ?? "1d",
  },
};
