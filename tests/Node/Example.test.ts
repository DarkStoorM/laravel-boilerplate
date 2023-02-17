import { expect, test } from "vitest";
import { Example } from "../../resources/js/Example";

// Example test just to force Vitest output
test("Does nothing", () => {
    const test = new Example();
    test.something();

    expect(test.str).toBe("something new");
});
