/// <reference types="vitest" />
import { defineConfig } from "vite";
import type { UserConfig as VitestUserConfigInterface } from "vitest/config";

const vitestConfig: VitestUserConfigInterface = {
    test: {
        globals: true,
        typecheck: { tsconfig: "./tsconfig-noncomposite-base.json" },
        coverage: {
            reporter: ["text", "json", "html"],
            provider: "istanbul",
            reportsDirectory: "tests/reports",
        },
    },
};

export default defineConfig({
    test: vitestConfig.test,
});
