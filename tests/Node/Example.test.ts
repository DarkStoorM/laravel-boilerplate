import { Example } from "../../resources/js/Example";

describe("Example Jest Test", () => {
    // Example test just to force Jest output
    it("Does nothing", () => {
        const test = new Example();
        test.something();

        expect(test.str).toBe("something new");
    });
});
