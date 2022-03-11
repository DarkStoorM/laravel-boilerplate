module.exports = {
  coverageDirectory: "tests/reports-node",
  globals: {
    "ts-jest": {
      tsconfig: "tsconfig-noncomposite.json",
    },
  },
  moduleFileExtensions: ["ts", "js"],
  transform: {
    "^.+\\.ts$": "ts-jest",
  },
  testMatch: ["**/tests/Node/**/*.test.(ts|js)"],
  testEnvironment: "node",
};